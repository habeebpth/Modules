<?php

namespace Modules\Events\Http\Controllers;

use App\Events\ContractSignedEvent;
use App\Helper\Files;
use CURLFile;
use App\Helper\Reply;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\SignRequest;
use App\Http\Requests\EstimateAcceptRequest;
use App\Models\AcceptEstimate;
use App\Models\Contract;
use App\Models\ContractSign;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\EstimateItemImage;
use App\Models\Invoice;
use App\Models\InvoiceItemImage;
use App\Models\InvoiceItems;
use App\Models\PublicContract;
use App\Models\PublicContractField;
use App\Models\PublicContractFieldsResponse;
use App\Models\PublicContractsResponse;
use App\Models\SmtpSetting;
use App\Scopes\ActiveScope;
use App\Traits\UniversalSearchTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Modules\Events\Entities\EvntEvent;
use Modules\Events\Entities\EventRegistration;
use Modules\Events\Entities\Panchayat;
use Modules\Events\Entities\District;
use Modules\Events\Entities\EventStudent;
use Nette\Utils\Random;
use Nwidart\Modules\Facades\Module;
// use Storage;
use setasign\Fpdi\Fpdi;
use Modules\Events\Entities\EventQueueStatus;

class PublicUrlController extends Controller
{
    use UniversalSearchTrait;

    /* Contract */
    public function EventPublicUrlView(Request $request, $slug)
    {
        $pageTitle = "app.menu.EventRegistration";
        $pageIcon = "fa fa-file";
        $EvntEvent = EvntEvent::where("slug", $slug)
            ->withoutGlobalScope(ActiveScope::class)
            ->firstOrFail();
        $eventstudents = EventStudent::all();
        // Ensure maximum_participants_per_user exists
        $allottedEnd = $EvntEvent->maximum_participants_per_user ?? 0;

        // Generate allowed seats list
        $allowedSeats = $allottedEnd > 0 ? range(1, $allottedEnd) : [];

        $countries = countries();

        return view("events::evnt-events.event-public-url", [
            "EvntEvent" => $EvntEvent,
            "pageTitle" => $pageTitle,
            "pageIcon" => $pageIcon,
            "countries" => $countries,
            "eventstudents" => $eventstudents,
            "allowedSeats" => $allowedSeats, // Pass the seat numbers to the view
        ]);
    }
    public function EventPublicUrlNewView(Request $request, $slug)
    {
        $pageTitle = "app.menu.EventRegistration";
        $pageIcon = "fa fa-file";
        $EvntEvent = EvntEvent::where("slug", $slug)
            ->withoutGlobalScope(ActiveScope::class)
            ->firstOrFail();
        $districts = District::all();
        $panchayath = Panchayat::all();
        // Ensure maximum_participants_per_user exists
        // $allottedEnd = $EvntEvent->maximum_participants_per_user ?? 0;

        // // Generate allowed seats list
        // $allowedSeats = $allottedEnd > 0 ? range(1, $allottedEnd) : [];

        $countries = countries();

        return view("events::evnt-events.registration-public-url", [
            "EvntEvent" => $EvntEvent,
            "pageTitle" => $pageTitle,
            "pageIcon" => $pageIcon,
            "countries" => $countries,
            "districts" => $districts, // Pass the seat numbers to the view
            "panchayath" => $panchayath, // Pass the seat numbers to the view

        ]);
    }


    public function EventRegister(Request $request)
    {

        $request->validate([
            'event_id' => 'required|integer|exists:evnt_events,id',
            'student_id' => 'required|string|exists:event_students,student_id',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'no_of_participants' => 'required|integer',
            'country_phonecode' => 'required|integer',
            'kids_under_12' => 'nullable|integer',
        ]);

        $event = EvntEvent::findOrFail($request->event_id);

        $totalParticipant = EventRegistration::where('event_id', $event->id)->sum('no_of_participants');
        $remainingSlots = $event->no_of_seats_for_participants - $totalParticipant;


        if ($request->no_of_participants > $remainingSlots) {
            return Reply::error(__('messages.participantLimitExceeded'));
        }

        $existingRegistration = EventRegistration::where('event_id', $event->id)
            ->where('student_id', $request->student_id)
            ->first();

        //   dd($existingRegistration);
        $registrationCode = mt_rand(100000, 999999);
        $maxSeatEnd = EventRegistration::where('event_id', $event->id)->max('allotted_seats_end');
        $allottedStart = is_null($maxSeatEnd) ? $event->participants_seat_start : $maxSeatEnd + 1;
        $allottedEnd = $allottedStart + $request->no_of_participants - 1;

        if ($existingRegistration) {
            $existingRegistration->no_of_participants += $request->no_of_participants;
            $existingRegistration->allotted_seats_start = $allottedStart;
            $existingRegistration->allotted_seats_end = $allottedEnd;
            $existingRegistration->registration_code = $registrationCode;
            $existingRegistration->name = $request->name;
            $existingRegistration->mobile = $request->mobile;
            $existingRegistration->country_phonecode = $request->country_phonecode;
            $existingRegistration->kids_under_12 = $request->kids_under_12 ?? 0;
            // Generate and store the PDF locally
            $existingRegistration->save();

            $registration = $existingRegistration;
        } else {
            $registration = new EventRegistration();
            $registration->company_id = $event->company_id;
            $registration->event_id = $event->id;
            $registration->student_id = $request->student_id;
            $registration->name = $request->name;
            $registration->mobile = $request->mobile;
            $registration->country_phonecode = $request->country_phonecode;
            $registration->no_of_participants = $request->no_of_participants;
            $registration->allotted_seats_start = $allottedStart;
            $registration->allotted_seats_end = $allottedEnd;
            $registration->registration_code = $registrationCode;
            $registration->kids_under_12 = $request->kids_under_12 ?? 0;
            // Generate and store the PDF locally

            $registration->save();
        }

        // Generate QR Code
        $qrCodeContent = route('event.qr.show', [
            'slug' => $event->slug,
            'registration_code' => $registration->registration_code,
        ]);

        $qrCode = new QrCode($qrCodeContent);
        $writer = new PngWriter();
        $qrImage = $writer->write($qrCode);
        $qrCodePath = 'qrcodes/' . uniqid() . '.png';
        Storage::disk('public')->put($qrCodePath, $qrImage->getString());


        $registration->qr_code = $qrCodePath;
        $pdfPath = $this->generateAndSaveEventPdf($event, $registration);
        // Optional: store pdf path in DB if needed
        $registration->pdf_path = $pdfPath;
        $registration->save();

        // Prepare data for message and document
        // $data = [
        //     'mobile'           => $registration->country_phonecode . $registration->mobile,
        //     'phoneNumberId'    => '367559613104315',
        //     'templateName'     => 'event_registration_success_1',
        //     'name'             => $registration->name,
        //     'documentFilename' => 'Event Entry Pass',
        //     'eventName'        => $event->name,
        //     'bestRegardsBy'    => 'Principal',
        //     'schoolName'       => 'Beaconhouse Al Khaleej International School',
        //     'filePath'         => asset('storage/' . $registration->pdf_path),
        //     'accessToken'      => 'EAALKlhr7IkoBO7ZBQJzyDZAblXpt2ePbyE799Fmwi19OuXM3qmVsVT2R2bo4dRpQUIXqlxtMJP3m5K7fjNYctSTPzZBKu9qXd0h0OafKh3eLBGe4fMhSjRpiYS9awWWW6SHzDcjzBoDkr1G6ho0VFS2qDxmzEA2oBonOpVeSnzEZADm1MANlcCZCWOcgnkZBKGMAZDZD',
        // ];

        // $output_file_url = $this->file_upload($data);
        // $this->sendMessage($data, $output_file_url);

        return Reply::successWithData(__('messages.registrationSuccess'), [
            'redirectUrl' => route('event.qr.show', [
                'slug' => $event->slug,
                'registration_code' => $registration->registration_code,
            ])
        ]);
    }

    private function generateAndSaveEventPdf($event, $registration)
    {
        // Absolute path to QR code
        $qrCodeUrl = public_path('storage/' . $registration->qr_code);
        $student = EventStudent::where('student_id', $registration->student_id)->first();
        // Background image
        $backgroundPath = public_path('img/event/shared image.jpg');
        $backgroundImage = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($backgroundPath));

        // Generate PDF
        $pdf = PDF::loadView('events::evnt-events.download-event-qr', [
            'EvntEvent'       => $event,
            'registration'    => $registration,
            'qrCodeUrl'       => $qrCodeUrl,
            'backgroundImage' => $backgroundImage,
            'student' => $student
        ])->setPaper('A4', 'portrait');

        // Store PDF
        $fileName = 'qrcodes/pdf_' . uniqid() . '.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        return $fileName;
    }


    public function getPanchayats($id)
    {
        $panchayats = Panchayat::where('district_id', $id)->get();

        return Reply::dataOnly(['status' => 'success', 'data' => $panchayats]);
    }

    public function NewEventRegister(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'event_id' => 'required|integer|exists:evnt_events,id',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'country_phonecode' => 'required|integer',
            'whatsapp' => 'nullable|string|max:20',
            'country_wtsap_phonecode' => 'required|integer',
            'place' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:20',
            'age' => 'nullable|integer',
            'sex' => 'nullable|string|in:male,female,other',
        ]);

        // Fetch the event details
        $event = EvntEvent::findOrFail($request->event_id);

        // Generate a unique registration code
        $registrationCode = mt_rand(100000, 999999);

        $sex = $request->sex;
        $prefix = $sex == 'male' ? 'G' : 'L';
        $latest_reg_no = EventRegistration::where('event_id', $request->event_id)->where('sex', $sex)->whereNotNull('registration_no')->orderBy('registration_no', 'desc')->first();

        $reg_no = $latest_reg_no ? $latest_reg_no->registration_no + 1 : 101;
        $registrationCode = $prefix . $reg_no;
        // Save the registration details

        $check_exist = EventRegistration::where('event_id', $request->event_id)->where('sex', $sex)->where('mobile', $request->mobile)->first();
        if (!$check_exist) {
            $registration = new EventRegistration();
            $registration->company_id = $event->company_id;
            $registration->event_id = $request->event_id;
            $registration->name = $request->name;
            $registration->mobile = $request->mobile;
            $registration->country_phonecode = $request->country_phonecode;
            $registration->country_wtsap_phonecode = $request->country_wtsap_phonecode;
            $registration->whatsapp = $request->whatsapp;
            $registration->place = $request->place;
            $registration->pincode = $request->pincode;
            $registration->age = $request->age;
            $registration->sex = $request->sex;
            $registration->panchayat_id = $request->panchayath;
            $registration->district_id = $request->district;
            $registration->registration_code = $registrationCode;
            $registration->registration_no = $reg_no;
            $registration->kids_under_12 = $request->kids_under_12;
            $registration->whatsapp_group_permission = $request->whatsapp_group_permission;
            $registration->save();

            // Generate QR code for the registration
            $qrCodeContent = route('event.new.qr.show', [
                'slug' => $event->slug,
                'registration_code' => $registration->registration_code
            ]);
            $qrCode = new QrCode($qrCodeContent);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrCodePath = 'qrcodes/' . uniqid() . '.png';
            Storage::disk('public')->put($qrCodePath, $result->getString());

            // Save the QR code path in the registration record
            $registration->qr_code = $qrCodePath;
            $registration->save();
        } else {
            $registration = $check_exist;
            $qrCodePath = $registration->qr_code;
        }

        $data = [
            'mobile'            => $registration->country_wtsap_phonecode . $registration->whatsapp,
            'phoneNumberId'     => '379394621917932',
            'templateName'      => 'ksc_solution_reg_success_1',
            'documentFilename'  => 'Solution QR',
            'filePath'            => asset('storage/' . $qrCodePath),
            'reg_code' => $registration->registration_code,
            'accessToken'       => 'EAAOQ9RUIZB9EBO5dcTXR4IN64RsKnuifIRDG6g90ky2ccjdvmZCFZCE89bdMoQoYJT1hTXZCerCzeLlpMP9gA7sGZCW50KO1lEnELSIFYNlzKq4Xbo5w98zrX6zTeBjtGZA8ZBC8EaR0Tg00yPRTxXdtKF0bQuFfHWNjofAMW0d6CRZCRHve5AIiviEiKygsBMGyeAZDZD',
        ];
        // if ($registration->whatsapp == '9746937888') {
        $output_file_url = $this->file_uploadImg($data);

        $this->sendMessageNew($data, $output_file_url);
        // }



        // Return success response with the URL to view the QR code
        return Reply::successWithData(__('messages.registrationSuccess'), [
            'redirectUrl' => route('event.new.qr.show', [
                'slug' => $event->slug,
                'registration_code' => $registration->registration_code,
            ])
        ]);
    }

    public function qrshow($slug, $registration_code)
    {

        $pageTitle = "app.menu.EventRegistration";
        $pageIcon = "fa fa-file";
        // dd($registration_id);
        $EvntEvent = EvntEvent::where("slug", $slug)
            ->withoutGlobalScope(ActiveScope::class)
            ->firstOrFail();

        $registration = EventRegistration::where('event_id', $EvntEvent->id)
            ->where('registration_code', $registration_code)
            ->firstOrFail();
        $student = EventStudent::where('student_id', $registration->student_id)->first();
        // dd($student);
        // Ensure QR code exists
        if (!$registration->qr_code || !Storage::disk('public')->exists($registration->qr_code)) {
            abort(404, 'QR Code not found.');
        }

        // Get QR code URL
        $qrCodeUrl = asset('storage/' . $registration->qr_code);

        return view('events::evnt-events.event_qr', compact('qrCodeUrl', 'EvntEvent', 'pageIcon', 'pageTitle', 'registration_code', 'registration', 'student'));
    }
    public function Newqrshow($slug, $registration_code)
    {

        $pageTitle = "app.menu.EventRegistration";
        $pageIcon = "fa fa-file";
        // dd($registration_id);
        $EvntEvent = EvntEvent::where("slug", $slug)
            ->withoutGlobalScope(ActiveScope::class)
            ->firstOrFail();

        $registration = EventRegistration::where('event_id', $EvntEvent->id)
            ->where('registration_code', $registration_code)
            ->firstOrFail();

        // Ensure QR code exists
        if (!$registration->qr_code || !Storage::disk('public')->exists($registration->qr_code)) {
            abort(404, 'QR Code not found.');
        }

        // Get QR code URL
        $qrCodeUrl = asset('storage/' . $registration->qr_code);

        return view('events::evnt-events.event_new_qr', compact('qrCodeUrl', 'EvntEvent', 'pageIcon', 'pageTitle', 'registration_code', 'registration'));
    }


    public function EventQrDownload($id, $registration_code)
    {
        set_time_limit(600);

        $EvntEvent = EvntEvent::where("slug", $id)
            ->withoutGlobalScope(ActiveScope::class)
            ->firstOrFail();

        $registration = EventRegistration::where('event_id', $EvntEvent->id)
            ->where('registration_code', $registration_code)
            ->firstOrFail();
        $student = EventStudent::where('student_id', $registration->student_id)->first();
        if (!$registration->qr_code || !Storage::disk('public')->exists($registration->qr_code)) {
            abort(404, 'QR Code not found.');
        }

        $qrCodeUrl = public_path('storage/' . $registration->qr_code); // Use absolute path

        // Background image (convert to base64 or use public_path)
        $backgroundPath = public_path('img/event/shared image.jpg');
        $backgroundImage = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($backgroundPath));

        // Load the PDF view
        $pdf = PDF::loadView('events::evnt-events.download-event-qr', compact('EvntEvent', 'registration', 'qrCodeUrl', 'backgroundImage','student'))
            ->setPaper('A4', 'portrait');

        return $pdf->download("QR-Code-Event-" . $EvntEvent->slug . ".pdf");
    }

    public function EventNewQrDownload($id, $registration_code)
    {
        set_time_limit(600); // Increase execution time

        $EvntEvent = EvntEvent::where("slug", $id)
            ->withoutGlobalScope(ActiveScope::class)
            ->firstOrFail();

        $registration = EventRegistration::where('event_id', $EvntEvent->id)
            ->where('registration_code', $registration_code)
            ->firstOrFail();

        if (!$registration->qr_code || !Storage::disk('public')->exists($registration->qr_code)) {
            abort(404, 'QR Code not found.');
        }

        $qrCodeUrl = asset('storage/' . $registration->qr_code);

        // Load HTML view for PDF
        $pdf = PDF::loadView('events::evnt-events.download-event-new_qr', compact('EvntEvent', 'registration', 'qrCodeUrl'))
            ->setPaper('A4', 'portrait')
            ->setOption('isRemoteEnabled', true); // Allow external images

        return $pdf->download("QR-Code-Event-" . $EvntEvent->slug . ".pdf");
    }
    public function estimateView($hash)
    {
        $estimate = Estimate::with(
            "client",
            "clientdetails",
            "clientdetails.user.country",
            "unit"
        )
            ->where("hash", $hash)
            ->firstOrFail();
        $company = $estimate->company;
        $defaultAddress = $company->defaultAddress;
        $pageTitle = $estimate->estimate_number;
        $pageIcon = "icon-people";
        $this->discount = 0;

        if ($estimate->discount > 0) {
            if ($estimate->discount_type == "percent") {
                $this->discount =
                    ($estimate->discount / 100) * $estimate->sub_total;
            } else {
                $this->discount = $estimate->discount;
            }
        }

        $taxList = [];

        $items = EstimateItem::whereNotNull("taxes")
            ->where("estimate_id", $estimate->id)
            ->get();

        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();

                if ($this->tax) {
                    if (
                        !isset(
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"]
                        )
                    ) {
                        if (
                            $estimate->calculate_tax == "after_discount" &&
                            $this->discount > 0
                        ) {
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"] =
                                ($item->amount -
                                    ($item->amount / $estimate->sub_total) *
                                    $this->discount) *
                                ($this->tax->rate_percent / 100);
                        } else {
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"] =
                                $item->amount *
                                ($this->tax->rate_percent / 100);
                        }
                    } else {
                        if (
                            $estimate->calculate_tax == "after_discount" &&
                            $this->discount > 0
                        ) {
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"] =
                                $taxList[$this->tax->tax_name .
                                    ": " .
                                    $this->tax->rate_percent .
                                    "%"] +
                                ($item->amount -
                                    ($item->amount / $estimate->sub_total) *
                                    $this->discount) *
                                ($this->tax->rate_percent / 100);
                        } else {
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"] =
                                $taxList[$this->tax->tax_name .
                                    ": " .
                                    $this->tax->rate_percent .
                                    "%"] +
                                $item->amount *
                                ($this->tax->rate_percent / 100);
                        }
                    }
                }
            }
        }

        $taxes = $taxList;

        $this->invoiceSetting = $company->invoiceSetting;

        return view("estimate", [
            "estimate" => $estimate,
            "taxes" => $taxes,
            "company" => $company,
            "discount" => $this->discount,
            "pageTitle" => $pageTitle,
            "pageIcon" => $pageIcon,
            "invoiceSetting" => $this->invoiceSetting,
            "defaultAddress" => $defaultAddress,
        ]);
    }

    public function estimateAccept(EstimateAcceptRequest $request, $id)
    {
        DB::beginTransaction();

        $estimate = Estimate::with("sign")->findOrFail($id);
        $company = $estimate->company;

        /** @phpstan-ignore-next-line */
        if ($estimate && $estimate->sign) {
            return Reply::error(__("messages.alreadySigned"));
        }

        $accept = new AcceptEstimate();
        $accept->company_id = $estimate->company->id;
        $accept->full_name = $request->first_name . " " . $request->last_name;
        $accept->estimate_id = $estimate->id;
        $accept->email = $request->email;
        $imageName = null;

        if ($request->signature_type == "signature") {
            $image = $request->signature; // your base64 encoded
            $image = str_replace("data:image/png;base64,", "", $image);
            $image = str_replace(" ", "+", $image);
            $imageName = str_random(32) . "." . "jpg";

            Files::createDirectoryIfNotExist("estimate/accept");

            File::put(
                public_path() .
                    "/" .
                    Files::UPLOAD_FOLDER .
                    "/estimate/accept/" .
                    $imageName,
                base64_decode($image)
            );
            Files::uploadLocalFile(
                $imageName,
                "estimate/accept",
                $estimate->company_id
            );
        } else {
            if ($request->hasFile("image")) {
                $imageName = Files::uploadLocalOrS3(
                    $request->image,
                    "estimate/accept/",
                    300
                );
            }
        }

        $accept->signature = $imageName;
        $accept->save();

        $estimate->status = "accepted";
        $estimate->saveQuietly();

        $invoice = new Invoice();

        $invoice->company_id = $company->id;
        $invoice->client_id = $estimate->client_id;
        $invoice->issue_date = now($company->timezone)->format("Y-m-d");
        $invoice->due_date = now($company->timezone)
            ->addDays($company->invoiceSetting->due_after)
            ->format("Y-m-d");
        $invoice->sub_total = round($estimate->sub_total, 2);
        $invoice->discount = round($estimate->discount, 2);
        $invoice->discount_type = $estimate->discount_type;
        $invoice->total = round($estimate->total, 2);
        $invoice->currency_id = $estimate->currency_id;
        $invoice->note = trim_editor($estimate->note);
        $invoice->status = "unpaid";
        $invoice->estimate_id = $estimate->id;
        $invoice->invoice_number = Invoice::lastInvoiceNumber() + 1;
        $invoice->save();

        /** @phpstan-ignore-next-line */
        foreach ($estimate->items as $item):
            if (!is_null($item)) {
                $invoiceItem = InvoiceItems::create([
                    "invoice_id" => $invoice->id,
                    "item_name" => $item->item_name,
                    "item_summary" => $item->item_summary ?: "",
                    "type" => "item",
                    "quantity" => $item->quantity,
                    "unit_price" => round($item->unit_price, 2),
                    "amount" => round($item->amount, 2),
                    "taxes" => $item->taxes,
                ]);

                $estimateItemImage = $item->estimateItemImage;

                if (!is_null($estimateItemImage)) {
                    $file = new InvoiceItemImage();

                    $file->invoice_item_id = $invoiceItem->id;

                    $fileName = Files::generateNewFileName(
                        $estimateItemImage->filename
                    );

                    Files::copy(
                        EstimateItemImage::FILE_PATH .
                            "/" .
                            $estimateItemImage->item->id .
                            "/" .
                            $estimateItemImage->hashname,
                        InvoiceItemImage::FILE_PATH .
                            "/" .
                            $invoiceItem->id .
                            "/" .
                            $fileName
                    );

                    $file->filename = $estimateItemImage->filename;
                    $file->hashname = $fileName;
                    $file->size = $estimateItemImage->size;
                    $file->save();
                }
            }
        endforeach;

        // Log search
        $this->logSearchEntry(
            $invoice->id,
            $invoice->invoice_number,
            "invoices.show",
            "invoice"
        );

        DB::commit();

        return Reply::success(__("messages.estimateSigned"));
    }

    public function estimateDecline(Request $request, $id)
    {
        $estimate = Estimate::findOrFail($id);
        $estimate->status = "declined";
        $estimate->saveQuietly();

        return Reply::dataOnly(["status" => "success"]);
    }

    public function estimateDownload($id)
    {
        $this->estimate = Estimate::with("client", "clientdetails")
            ->where("hash", $id)
            ->firstOrFail();
        $this->invoiceSetting = $this->estimate->company->invoiceSetting;
        App::setLocale($this->invoiceSetting->locale ?? "en");
        Carbon::setLocale($this->invoiceSetting->locale ?? "en");

        $pdfOption = $this->domPdfObjectForDownload($id);
        $pdf = $pdfOption["pdf"];
        $filename = $pdfOption["fileName"];

        return $pdf->download($filename . ".pdf");
    }

    public function domPdfObjectForDownload($id)
    {
        $this->estimate = Estimate::where("hash", $id)->firstOrFail();
        $this->company = $this->estimate->company;
        $this->invoiceSetting = $this->company->invoiceSetting;
        App::setLocale($this->invoiceSetting->locale ?? "en");
        Carbon::setLocale($this->invoiceSetting->locale ?? "en");

        $this->discount = 0;

        if ($this->estimate->discount > 0) {
            if ($this->estimate->discount_type == "percent") {
                $this->discount =
                    ($this->estimate->discount / 100) *
                    $this->estimate->sub_total;
            } else {
                $this->discount = $this->estimate->discount;
            }
        }

        $taxList = [];

        $items = EstimateItem::whereNotNull("taxes")
            ->where("estimate_id", $this->estimate->id)
            ->get();

        foreach ($items as $item) {
            foreach (json_decode($item->taxes) as $tax) {
                $this->tax = EstimateItem::taxbyid($tax)->first();

                if ($this->tax) {
                    if (
                        !isset(
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"]
                        )
                    ) {
                        if (
                            $this->estimate->calculate_tax ==
                            "after_discount" &&
                            $this->discount > 0
                        ) {
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"] =
                                ($item->amount -
                                    ($item->amount /
                                        $this->estimate->sub_total) *
                                    $this->discount) *
                                ($this->tax->rate_percent / 100);
                        } else {
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"] =
                                $item->amount *
                                ($this->tax->rate_percent / 100);
                        }
                    } else {
                        if (
                            $this->estimate->calculate_tax ==
                            "after_discount" &&
                            $this->discount > 0
                        ) {
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"] =
                                $taxList[$this->tax->tax_name .
                                    ": " .
                                    $this->tax->rate_percent .
                                    "%"] +
                                ($item->amount -
                                    ($item->amount /
                                        $this->estimate->sub_total) *
                                    $this->discount) *
                                ($this->tax->rate_percent / 100);
                        } else {
                            $taxList[$this->tax->tax_name .
                                ": " .
                                $this->tax->rate_percent .
                                "%"] =
                                $taxList[$this->tax->tax_name .
                                    ": " .
                                    $this->tax->rate_percent .
                                    "%"] +
                                $item->amount *
                                ($this->tax->rate_percent / 100);
                        }
                    }
                }
            }
        }

        $this->taxes = $taxList;

        $pdf = app("dompdf.wrapper");
        $pdf->setOption("enable_php", true);
        $pdf->setOption("isHtml5ParserEnabled", true);
        $pdf->setOption("isRemoteEnabled", true);

        $pdf->loadView(
            "estimates.pdf." . $this->invoiceSetting->template,
            $this->data
        );

        $filename = $this->estimate->estimate_number;

        return [
            "pdf" => $pdf,
            "fileName" => $filename,
        ];
    }

    public function checkEnv()
    {
        $plugins = Module::all(); /* @phpstan-ignore-line */
        $updateArray = [];
        $updateArrayEnabled = [];

        foreach ($plugins as $key => $plugin) {
            $modulePath = $plugin->getPath();
            $version = trim(File::get($modulePath . "/version.txt"));

            if ($plugin->isEnabled()) {
                $updateArrayEnabled[$key] = $version;
            }

            $updateArray[$key] = $version;
        }

        $smtpVerified = SmtpSetting::value("verified");

        return [
            "app" => config("froiden_envato.envato_product_name"),
            "redirect_https" => config("app.redirect_https"),
            "version" => trim(File::get("version.txt")),
            "debug" => config("app.debug"),
            "queue" => config("queue.default"),
            "php" => phpversion(),
            "environment" => app()->environment(),
            "smtp_verified" => $smtpVerified,
            "all_modules" => $updateArray,
            "modules_enabled" => $updateArrayEnabled,
        ];
    }

    public function publicContractView(Request $request, $url_id)
    {
        $pageTitle = "app.menu.contracts";
        $pageIcon = "fa fa-file";

        $contract = PublicContract::where("url_id", $url_id)
            ->with("fields", "company")
            ->firstOrFail();

        $company = $contract->company;
        $invoiceSetting = $contract->company->invoiceSetting;
        $fields = [];

        $fields = $contract->fields;

        return view("public-contract", [
            "contract" => $contract,
            "company" => $company,
            "pageTitle" => $pageTitle,
            "pageIcon" => $pageIcon,
            "invoiceSetting" => $invoiceSetting,
            "fields" => $fields,
        ]);
    }

    public function saveSignatures(Request $request, $id)
    {
        $publicContractResponse = new PublicContractsResponse();
        $publicContractResponse->public_contract_id = $id;
        $publicContractResponse->contract_url = md5(Random::generate(10));
        $publicContractResponse->save();

        $uniqueFields = [];

        $publicContractFieldResponseArray = [];

        $showError = false;
        $errorField = "";

        $fields = json_decode($request->fields);
        foreach ($fields as $key => $field) {
            if ($field == null) {
                continue;
            }
            $publicContractFieldResponse = new PublicContractFieldsResponse();
            $publicContractFieldResponse->public_contracts_response_id =
                $publicContractResponse->id;
            $publicContractFieldResponse->public_contract_id = $id;
            $publicContractFieldResponse->public_contract_field_id = $key;
            $publicContractFieldResponse->value = $field;
            $publicContractFieldResponseArray[] = $publicContractFieldResponse;
            if (PublicContractField::find($key)->unique == "Y") {
                $uniqueFields[] = array_key_last(
                    $publicContractFieldResponseArray
                );
            }
            if (
                PublicContractField::find($key)->required == 1 &&
                empty($field)
            ) {
                $showError = true;
                $errorField .= PublicContractField::find($key)->name . ", ";
            }
        }

        if (!$showError) {
            foreach ($uniqueFields as $key) {
                if (
                    PublicContractFieldsResponse::where(
                        "public_contract_field_id",
                        $publicContractFieldResponseArray[$key]
                            ->public_contract_field_id
                    )
                    ->where(
                        "public_contract_id",
                        $publicContractFieldResponseArray[$key]
                            ->public_contract_id
                    )
                    ->where(
                        "value",
                        $publicContractFieldResponseArray[$key]->value
                    )
                    ->exists()
                ) {
                    if ($showError) {
                        $errorField .= ", ";
                    }

                    $showError = true;
                    $errorField .= PublicContractField::find(
                        $publicContractFieldResponseArray[$key]
                            ->public_contract_field_id
                    )->name;
                } else {
                    $showError = false;
                    break;
                }
            }
        }

        $signatures = json_decode($request->signature);

        foreach ($signatures as $key => $signature) {
            if ($signature == null) {
                continue;
            }
            $publicContractSignature = new PublicContractFieldsResponse();
            $publicContractSignature->public_contracts_response_id =
                $publicContractResponse->id;
            $publicContractSignature->public_contract_id = $id;
            $publicContractSignature->public_contract_field_id = $key;
            if (
                PublicContractField::find($key)->required == 1 &&
                empty($signature)
            ) {
                $showError = true;
                $errorField .= PublicContractField::find($key)->name . ", ";
            }
            $imageData = base64_decode(explode(",", $signature)[1]);
            $image = imagecreatefromstring($imageData);
            $path =
                "public/signatures/" .
                $id .
                "-" .
                $key .
                "-" .
                \Str::random(10, "alnum") .
                ".png";

            ob_start();
            imagepng($image);
            $imageData = ob_get_contents();
            ob_end_clean();

            Storage::put($path, $imageData);
            imagedestroy($image);

            $publicContractSignature->value = $path;
            if (!$showError) {
                $publicContractSignature->save();
            }
        }

        if ($showError) {
            $publicContractResponse->delete();
            return Reply::error(
                __("messages.uniqueField", ["field" => $errorField])
            );
        }

        if (!$showError) {
            foreach ($publicContractFieldResponseArray as $field) {
                $field->save();
            }
        }

        return response()->json(
            [
                "status" => "success",
                "message" => "Signature saved successfully.",
                "contract_url" => ($signedUrl = route(
                    "public_contract.download",
                    $publicContractResponse->contract_url
                )),
            ],
            200
        );
    }

    // https://erpbeta.cloudocz.com/contract/download/3eebe0a344fdb762e14271b0b7214178
    public function downloadContract($contract_url)
    {
        $contractResponse = PublicContractsResponse::where(
            "contract_url",
            $contract_url
        )
            ->latest()
            ->firstOrFail();
        $contract = $contractResponse->publicContract;
        $fields = PublicContractFieldsResponse::with("publicContractField")
            ->where("public_contracts_response_id", $contractResponse->id)
            ->get();

        $this->invoiceSetting = $contract->company->invoiceSetting;

        $filePath = public_path("user-uploads/" . $contract->pdf);

        $uniqueFieldNames = "";

        foreach ($fields as $field) {
            if (
                $field->publicContractField->unique == "Y" &&
                $field->publicContractField->type != "signature"
            ) {
                $uniqueFieldNames .= $field->value . "_";
            }
        }

        $outputFilePath = public_path(
            "user-uploads/" . $uniqueFieldNames . $contract->name . ".pdf"
        );

        $this->fillPDFFile($filePath, $outputFilePath, $fields);

        return response()->file($outputFilePath);
    }

    public function fillPDFFile($file, $outputFilePath, $fields)
    {
        $fpdi = new FPDI();

        $count = $fpdi->setSourceFile($file);

        for ($i = 1; $i <= $count; $i++) {
            $template = $fpdi->importPage($i);

            $size = $fpdi->getTemplateSize($template);

            $fpdi->AddPage($size["orientation"], [
                $size["width"],
                $size["height"],
            ]);

            $fpdi->useTemplate($template);

            foreach ($fields as $key => $field) {
                if ($i != $field->publicContractField->page_number) {
                    continue;
                }

                $fpdi->SetFont("helvetica", "", 10);

                $fpdi->SetTextColor(0, 0, 0);

                $left = $field->publicContractField->position_left;

                $top = $field->publicContractField->position_top;

                $text = $field->value;

                if ($field->publicContractField->type == "signature") {
                    $imagePath = public_path("user-uploads/" . $text);
                    $x = $left;
                    $y = $top;
                    if (!file_exists($imagePath)) {
                        throw new Exception(
                            "Image file does not exist: " . $imagePath
                        );
                    }
                    $fpdi->Image($imagePath, $x, $y, 40, 20);
                    continue;
                }

                $fpdi->Text($left, $top, $text);
            }
        }

        return $fpdi->Output($outputFilePath, "F");
    }

    public function sendMessage($data, $mediaId)
    {
        $accessToken     = $data['accessToken'];
        $phoneNumberId     = $data['phoneNumberId'];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v20.0/$phoneNumberId/messages");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ]);

        // Define the POST data
        $postData = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $data['mobile'],
            'type' => 'template',
            'template' => [
                'name' =>  $data['templateName'],
                'language' => [
                    'code' => 'en'
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'document',
                                'document' => [
                                    'id' => $mediaId,
                                    'filename' => $data['documentFilename']
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $data['name']
                            ],
                            [
                                'type' => 'text',
                                'text' => $data['eventName']
                            ],
                            [
                                'type' => 'text',
                                'text' => $data['bestRegardsBy']
                            ],
                            [
                                'type' => 'text',
                                'text' => $data['schoolName']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Convert POST data to JSON
        $jsonPostData = json_encode($postData);

        // Set the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostData);

        // Execute cURL request and capture the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            // echo 'Error:' . curl_error($ch);
        } else {
            // Decode and print the response
            $responseData = json_decode($response, true);
            // print_r($responseData);
        }

        // Close cURL session
        curl_close($ch);
    }

    public function sendMessageNew($data, $mediaId)
    {
        $accessToken     = $data['accessToken'];
        $phoneNumberId     = $data['phoneNumberId'];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v20.0/$phoneNumberId/messages");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ]);

        // Define the POST data
        $postData = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $data['mobile'],
            'type' => 'template',
            'template' => [
                'name' =>  $data['templateName'],
                'language' => [
                    'code' => 'ml'
                ],
                'components' => [
                    [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'id' => $mediaId,
                                    // 'filename' => $data['documentFilename']
                                ]
                            ]
                        ]
                    ],
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $data['reg_code']
                            ],
                        ]
                    ]
                ]
            ]
        ];

        // Convert POST data to JSON
        $jsonPostData = json_encode($postData);

        // Set the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostData);

        // Execute cURL request and capture the response
        $response = curl_exec($ch);

        // Check for errors
        // if (curl_errno($ch)) {
        //     echo 'Error:' . curl_error($ch);
        // } else {
        //     // Decode and print the response
        //     $responseData = json_decode($response, true);
        //     print_r($responseData);
        // }

        // Close cURL session
        curl_close($ch);
    }


    public function file_upload($data)
    {
        $accessToken     = $data['accessToken'];
        $phoneNumberId     = $data['phoneNumberId'];
        $filePath         = $data['filePath'];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v20.0/$phoneNumberId/media");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken"
        ]);

        // Define the file data to be uploaded
        $fileData = new CURLFile($filePath, 'application/pdf');


        // Set the POST fields
        $postFields = [
            'file' => $fileData,
            'type' => 'application/pdf',
            'messaging_product' => 'whatsapp'
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        // Execute cURL request and capture the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            $mediaId = "";
        } else {
            // Decode and print the response
            $responseData = json_decode($response, true);
            // print_r($responseData);

            $mediaId = $responseData['id'];
        }

        // Close cURL session
        curl_close($ch);

        return $mediaId;
    }

    public function file_uploadImg($data)
    {
        $accessToken     = $data['accessToken'];
        $phoneNumberId   = $data['phoneNumberId'];
        $filePath        = $data['filePath'];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v20.0/$phoneNumberId/media");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken"
        ]);

        // For PNG images, use the correct MIME type
        $fileData = new CURLFile($filePath, 'image/png');

        // Set the POST fields
        $postFields = [
            'file' => $fileData,
            'type' => 'image/png',
            'messaging_product' => 'whatsapp'
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        // Execute cURL request and capture the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            $mediaId = "";
        } else {
            // Decode and print the response
            $responseData = json_decode($response, true);
            // print_r($responseData);

            $mediaId = isset($responseData['id']) ? $responseData['id'] : "";
        }

        // Close cURL session
        curl_close($ch);

        return $mediaId;
    }

    public function EventQueueStatusView(Request $request, $slug)
    {
        $queu = EventQueueStatus::orderBy('id', 'desc')->first();
        return view("events::evnt-events.queue_status", [
            "gents" => $queu ? $queu->gents : '',
            "ladies" => $queu ? $queu->ladies : '',
        ]);
    }
}
