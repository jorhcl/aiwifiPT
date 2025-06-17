<?php


/**
 *
 * @author. Jorge Cortes Lopez
 *
 * Class   ContactController
 *   Controller to  contact upload, list and delete
 *
 *
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\DeleteContactRequest;
use App\Http\Requests\Contact\UploadContactsRequest;
use App\Http\Resources\ContactResource;
use App\Jobs\ProcessContactCsvFile;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    //

    protected ContactService $service;

    public function __construct(ContactService $service)
    {

        $this->service = $service;
    }

    public function uploadContacts(UploadContactsRequest $request)
    {
        $path = $request->file('file')->store('tmp');
        ProcessContactCsvFile::dispatch($path,  Auth::user()->id);

        return response()->json([
            'message' => 'Csv file loaded successfully',
        ], 202);
    }

    public function index(Request $request)
    {

        $records = $request->records ?? 10;

        $contactId = Auth::user()->id;

        $contacts = $this->service->getClientContacts($contactId, $records);
        return response()->json(
            [
                'contacts' => ContactResource::collection($contacts)
            ]
        );
    }

    public function destroy(DeleteContactRequest $request)
    {
        $clientId =  Auth::user()->id;
        $contactId = $request->validated()['id'];

        $this->service->deleteClientContact($clientId, $contactId);

        return response()->json([
            'message' => 'Contact deleted successfully'
        ], 200);
    }
}
