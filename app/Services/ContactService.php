<?php

/**
 *
 * @author. Jorge Cortes Lopez
 *
 * Class   ContactService
 *   Service to contacts table logic
 *
 *
 */


namespace App\Services;

use App\Models\Contact;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

/**
 *
 * @author. Jorge Cortes Lopez
 *
 * Class   ContactService
 *   Service to add logic to contact  flow
 *
 *
 */


class ContactService
{

    /**
     *
     * @var $mailboxLayerKey.  Api key to use the mailboxlayer api
     */
    protected $mailboxLayerKey;

    public function __construct()
    {
        $this->mailboxLayerKey = env('MAILBOXLAYER_API_KEY');
    }

    /**
     *  Function to parse rows in Csv file
     *
     *
     *  @param UploadedFile  $file
     *
     *  @return. array $rows.  array with csv rows
     */


    private function parseCsv(UploadedFile $file): array
    {

        $csv = Reader::createFromPath($file->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $rows = [];
        foreach ($records as $record) {
            $rows[] = array_map('trim', $record);
        }

        return $rows;
    }


    /**
     *
     *  Function to valid email
     *
     *  @param String $email.    Contact's email
     *
     *  @return array []          array with email data
     *
     *
     *
     */

    private function validEmailMailBoxLayer(String $email): array
    {
        try {
            if (!$this->mailboxLayerKey) {
                return [
                    'error' => true,
                    'data' => null,
                    'message' => 'Mailboxlayer API key missing',
                    'status' => 500
                ];
            }

            $response = Http::retry(3, function ($attempt) {

                return pow(2, $attempt) * 1000;
            }, function ($exception, $request) {

                return $exception->response?->status() === 429;
            })->get('https://apilayer.net/api/check', [
                'access_key' => $this->mailboxLayerKey,
                'email' => $email,
                'smtp' => 1,
                'format' => 1,
            ]);

            if ($response->failed()) {
                return [
                    'error' => true,
                    'data' => $response->json(),
                    'message' => 'Error in email validation',
                    'status' => $response->status()
                ];
            }

            $data = $response->json();

            return [
                'error' => false,
                'data' => $response->json(),
                'message' => null,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Exception in email validation with Mailboxlayer', [
                'email' => $email,
                'exception' => $e
            ]);

            return [
                'error' => true,
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 500
            ];
        }
    }


    /**
     *
     *  Function to get  contact gender
     *
     *  @param string $name.    Contact name
     *
     *  @return array []
     *
     */
    protected function getGender(String $name): array
    {
        try {
            $response =  Http::get('https://api.genderize.io', ['name' => $name]);
            if ($response->failed()) {
                return [
                    'error' => true,
                    'data' => $response->json(),
                    'message' => 'Error to get gender',
                    'status' => $response->status()
                ];
            }

            return [
                'error' => false,
                'data' => [
                    'gender' => $response->json('gender')
                ],
                'message' => null,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Exception to get contact gender', [
                'name' => $name,
                'exception' => $e
            ]);

            return [
                'error' => true,
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     *
     * Function to get age by name
     *
     * @param String $name     Contact name
     *
     * @return array []
     *
     */

    protected function getAge($name)
    {

        try {
            $response = Http::get('https://api.agify.io', ['name' => $name]);

            if ($response->failed()) {
                return [
                    'error' => true,
                    'data' => $response->json(),
                    'message' => 'Error to get age',
                    'status' => $response->status()
                ];
            }

            return [
                'error' => false,
                'data' => [
                    'age' => $response->json('age')
                ],
                'message' => null,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Exception to get contact age', [
                'name' => $name,
                'exception' => $e
            ]);

            return [
                'error' => true,
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 500
            ];
        }
    }



    /**
     *
     *  Get Contact nationality by name
     *
     *  @param String $name.     Contact name
     *
     *  @return array []
     *
     */

    protected function getNationality(String $name): array
    {
        try {
            $response = Http::get('https://api.nationalize.io', ['name' => $name]);

            if ($response->failed()) {
                return [
                    'error' => true,
                    'data' => $response->json(),
                    'message' => 'Error to get age',
                    'status' => $response->status()
                ];
            }

            $data = $response->json();
            return [
                'error' => false,
                'data' => [
                    'country' => $data['country'][0]['country_id'] ?? null
                ],
                'message' => null,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Exception to get contact country', [
                'name' => $name,
                'exception' => $e
            ]);

            return [
                'error' => true,
                'data' => null,
                'message' => $e->getMessage(),
                'status' => 500
            ];
        }
    }





    /**
     *
     *  Function to import contacts from CSV file
     *
     *  @param  UploadedFile $file.  Csv file to import contacts
     *
     *  @return array     Response to
     *
     */

    public function importContactsFromCsv(UploadedFile $file, int $clientId): void
    {



        $rows = $this->parseCsv($file);

        foreach ($rows as $row) {


            usleep(200000);
            $name = trim($row['name'] ?? '');
            $email = trim($row['email'] ?? '');
            $gender = null;
            $age = null;
            $nationality = null;

            if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Log::warning('Invalid contact ', ['name' => $name, 'email' => $email, 'client_id' => $clientId]);
                continue;
            }

            $emailInfo = $this->validEmailMailBoxLayer($email);




            if (!$emailInfo['data']['format_valid']) {
                Log::info('Email invalid', ['name' => $name, 'email' => $email, 'client_id' => $clientId]);
                continue;
            }

            $genderResponse = $this->getGender($name);
            if (!$genderResponse['error']) {
                $gender = $genderResponse['data']['gender'];
            }

            $ageResponse = $this->getAge($name);
            if (!$ageResponse['error']) {
                $age = $ageResponse['data']['age'];
            }

            $nationalityResponse = $this->getNationality($name);
            if (!$nationalityResponse['error']) {
                $nationality = $nationalityResponse['data']['country'];
            }


            try {
                Contact::updateOrCreate(
                    ['client_id' => $clientId, 'email' => $email],
                    [
                        'name' => $name,
                        'gender' => $gender,
                        'nationality' => $nationality,
                        'age' => $age,
                        'email_data' => $emailInfo['data']
                    ]
                );
            } catch (Exception $e) {
                Log::error('Error to add contact ', [
                    'email' => $email,
                    'name' => $name,
                    'client_id' => $clientId,
                    'error' => $e
                ]);
            }
        }
    }

    /**
     *
     *  Get client contacts
     *
     *  @param  int  $clientId.     Client id
     *  @param  int  $records.      Number of records per page
     *
     */

    public function getClientContacts(int $clientId, int $records): LengthAwarePaginator
    {
        return Contact::where('client_id', $clientId)->orderBy('id', 'desc')->paginate($records);
    }


    /**
     *
     *  Delete contact
     *
     *  @param int  $clientId.      Client Id
     *  @param int  $contactId      Contact Id to delete
     *
     */
    public function deleteClientContact(int $clientId, int $contactId): void
    {
        $contact = Contact::where('client_id', $clientId)->findOrFail($contactId);
        $contact->delete();
    }
}
