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

    function delete(Request $req, Event $event) {
        try {
            if ($event->user_id != $req->user()->id)
                return response()->json(["error" => ["You cannot delete this event"]]);

            $event->delete();

            return response()->json(["success" => ["Event successfully deleted."]]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json(
                ["error" => [$err->getMessage()]]
            );
        }
    }

    function edit(Request $req, Event $event) {
        try {
            if ($event->user_id != $req->user()->id)
                return response()->json(["error" => ["You cannot edit this event"]]);

            $validated_data = $req->validate([
                'description' => ''
            ]);

            $event->description = $validated_data['description'];
            $event->save();

            return response()->json(["success" => ["Event successfully edited."]]);
        } catch (ValidationException $err) {
            return response()->json(["validation_errors" => $err->validator->getMessageBag()]);
        } catch (Exception $err) {
            return response()->json(
                ["error" => [$err->getMessage()]]
            );
        }
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
