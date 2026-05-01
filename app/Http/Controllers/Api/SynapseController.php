<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PyrxSynapse\Client;
use PyrxSynapse\Errors\SynapseError;

class SynapseController extends Controller
{
    private function synapse(): Client
    {
        return new Client(
            apiKey: env('SYNAPSE_API_KEY'),
            workspaceId: env('SYNAPSE_WORKSPACE_ID'),
            baseUrl: env('SYNAPSE_API_URL', 'https://synapse-api.pyrx.tech')
        );
    }

    private function snakeKeys(array $hash): array
    {
        $result = [];
        foreach ($hash as $key => $value) {
            $snakeKey = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $key));
            $result[$snakeKey] = is_array($value) ? $this->snakeKeys($value) : $value;
        }
        return $result;
    }

    private function requestBody(Request $request): array
    {
        return $request->json()->all();
    }

    // POST /api/track
    public function track(Request $request): JsonResponse
    {
        try {
            $body = $this->requestBody($request);
            $result = $this->synapse()->track(
                externalId: $body['userId'] ?? '',
                eventName: $body['event'] ?? '',
                attributes: $body['attributes'] ?? []
            );
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // POST /api/track/batch
    public function trackBatch(Request $request): JsonResponse
    {
        try {
            $body = $this->requestBody($request);
            $events = array_map(
                fn($e) => $this->snakeKeys($e),
                $body['events'] ?? []
            );
            $result = $this->synapse()->trackBatch(events: $events);
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // POST /api/identify
    public function identify(Request $request): JsonResponse
    {
        try {
            $body = $this->requestBody($request);
            $result = $this->synapse()->identify(
                externalId: $body['userId'] ?? '',
                email: $body['email'] ?? '',
                properties: $body['properties'] ?? []
            );
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // POST /api/identify/batch
    public function identifyBatch(Request $request): JsonResponse
    {
        try {
            $body = $this->requestBody($request);
            $contacts = array_map(
                fn($c) => $this->snakeKeys($c),
                $body['contacts'] ?? []
            );
            $result = $this->synapse()->identifyBatch(contacts: $contacts);
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // POST /api/send
    public function sendEmail(Request $request): JsonResponse
    {
        try {
            $body = $this->snakeKeys($this->requestBody($request));
            $result = $this->synapse()->sendEmail(
                templateSlug: $body['template_slug'] ?? '',
                to: $body['to'] ?? [],
                attributes: $body['attributes'] ?? []
            );
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // GET /api/contacts
    public function listContacts(Request $request): JsonResponse
    {
        try {
            $result = $this->synapse()->contacts->list(
                page: (int) $request->query('page', 1),
                perPage: (int) $request->query('limit', 20)
            );
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // PUT /api/contacts/{id}
    public function updateContact(Request $request, string $id): JsonResponse
    {
        try {
            $result = $this->synapse()->contacts->update($id, $this->requestBody($request));
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // DELETE /api/contacts/{id}
    public function deleteContact(string $id): JsonResponse
    {
        try {
            $this->synapse()->contacts->delete($id);
            return response()->json(['success' => true]);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // GET /api/templates
    public function listTemplates(): JsonResponse
    {
        try {
            $result = $this->synapse()->templates->list();
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // POST /api/templates
    public function createTemplate(Request $request): JsonResponse
    {
        try {
            $result = $this->synapse()->templates->create($this->requestBody($request));
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // GET /api/templates/{slug}
    public function getTemplate(string $slug): JsonResponse
    {
        try {
            $result = $this->synapse()->templates->get($slug);
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // PUT /api/templates/{slug}
    public function updateTemplate(Request $request, string $slug): JsonResponse
    {
        try {
            $result = $this->synapse()->templates->update($slug, $this->requestBody($request));
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // POST /api/templates/{slug}/preview
    public function previewTemplate(Request $request, string $slug): JsonResponse
    {
        try {
            $result = $this->synapse()->templates->preview($slug, $this->requestBody($request));
            return response()->json($result);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }

    // DELETE /api/templates/{slug}
    public function deleteTemplate(string $slug): JsonResponse
    {
        try {
            $this->synapse()->templates->delete($slug);
            return response()->json(['success' => true]);
        } catch (SynapseError $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 500);
        }
    }
}
