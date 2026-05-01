<?php
/**
 * Minimal PHP router for Synapse SDK example.
 * Demonstrates the same 14 endpoints without framework overhead.
 * Run: php -S localhost:4011 -t public
 */

require_once __DIR__ . '/../vendor/autoload.php';

$client = new PyrxSynapse\Client(
    apiKey: getenv('SYNAPSE_API_KEY') ?: '',
    workspaceId: getenv('SYNAPSE_WORKSPACE_ID') ?: '',
    baseUrl: getenv('SYNAPSE_API_URL') ?: 'https://synapse-api.pyrx.tech'
);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$body = json_decode(file_get_contents('php://input'), true) ?? [];

// camelCase to snake_case helper
function snakeKeys(array $arr): array {
    if (array_is_list($arr)) return $arr; // don't convert numeric arrays
    $out = [];
    foreach ($arr as $k => $v) {
        $sk = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $k));
        $out[$sk] = is_array($v) && !array_is_list($v) ? snakeKeys($v) : $v;
    }
    return $out;
}

function jsonResponse($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

try {
    // Core
    if ($path === '/api/track' && $method === 'POST') {
        jsonResponse($client->track(
            externalId: $body['userId'] ?? '',
            eventName: $body['event'] ?? '',
            attributes: $body['attributes'] ?? []
        ));
    }
    if ($path === '/api/track/batch' && $method === 'POST') {
        $events = array_map('snakeKeys', $body['events'] ?? []);
        jsonResponse($client->trackBatch(events: $events));
    }
    if ($path === '/api/identify' && $method === 'POST') {
        jsonResponse($client->identify(
            externalId: $body['userId'] ?? '',
            email: $body['email'] ?? null,
            properties: $body['properties'] ?? null
        ));
    }
    if ($path === '/api/identify/batch' && $method === 'POST') {
        $contacts = array_map('snakeKeys', $body['contacts'] ?? []);
        jsonResponse($client->identifyBatch(contacts: $contacts));
    }
    if ($path === '/api/send' && $method === 'POST') {
        jsonResponse($client->sendEmail(
            templateSlug: $body['templateSlug'] ?? '',
            to: snakeKeys($body['to'] ?? []),
            attributes: $body['attributes'] ?? []
        ));
    }

    // Contacts
    if ($path === '/api/contacts' && $method === 'GET') {
        jsonResponse($client->contacts->list(
            page: (int)($_GET['page'] ?? 1),
            perPage: (int)($_GET['limit'] ?? 20)
        ));
    }
    if (preg_match('#^/api/contacts/([^/]+)$#', $path, $m)) {
        if ($method === 'PUT') jsonResponse($client->contacts->update($m[1], $body));
        if ($method === 'DELETE') { $client->contacts->delete($m[1]); jsonResponse(['success' => true]); }
    }

    // Templates
    if ($path === '/api/templates' && $method === 'GET') jsonResponse($client->templates->list());
    if ($path === '/api/templates' && $method === 'POST') jsonResponse($client->templates->create($body));
    if (preg_match('#^/api/templates/([^/]+)/preview$#', $path, $m) && $method === 'POST') {
        jsonResponse($client->templates->preview($m[1], $body));
    }
    if (preg_match('#^/api/templates/([^/]+)$#', $path, $m)) {
        if ($method === 'GET') jsonResponse($client->templates->get($m[1]));
        if ($method === 'PUT') jsonResponse($client->templates->update($m[1], $body));
        if ($method === 'DELETE') { $client->templates->delete($m[1]); jsonResponse(['success' => true]); }
    }

    jsonResponse(['error' => 'Not found'], 404);
} catch (PyrxSynapse\Errors\SynapseError $e) {
    jsonResponse(['error' => $e->getMessage(), 'status' => $e->status], $e->status);
}
