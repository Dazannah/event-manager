<?php

namespace App\Http\Controllers;

use Exception;
use App\Interfaces\IEventService;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventController extends Controller {
    private IEventService $eventService;

    function __construct(IEventService $eventService) {
        $this->eventService = $eventService;
    }

    function getUserEvents(Request $req) {
        try {
            $events = $this->eventService->getUserEvents($req->user()->id);

            return response()->json($events);
        } catch (Exception $err) {
            return response()->json(
                ["error" => [$err->getMessage()]]
            );
        }
    }

    function create(Request $req) {
        try {
            $validated_data = $req->validate([
                'title' => 'required',
                'description' => ''
            ]);

            $this->eventService->create($validated_data, $req->user()->id);

            return response()->json(["success" => "Event successfully created"]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json(
                ["error" => [$err->getMessage()]]
            );
        }
    }
}
