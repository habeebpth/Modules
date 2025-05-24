<div class="row">
  <div class="col-sm-12">
    <x-form id="save-checkin-data-form" enctype="multipart/form-data">
      <div class="add-client bg-white rounded">
        <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
          @lang('app.reservationDetails')</h4>
        <div class="row px-4">
          <div class="col-lg-3 col-md-3">
            <x-forms.datepicker fieldId="check_in" :fieldLabel="__('app.checkIn')" fieldName="check_in" :fieldPlaceholder="__('placeholders.date')"
              fieldRequired="true" :fieldValue="now(company()->timezone)->format(company()->date_format)" />
          </div>
          <div class="col-lg-3 col-md-3">
            <x-forms.datepicker fieldId="check_out" :fieldLabel="__('app.checkOut')" fieldName="check_out" :fieldPlaceholder="__('placeholders.date')"
              fieldRequired="true" :fieldValue="now(company()->timezone)->format(company()->date_format)" />
          </div>
          <div class="col-md-3">
            <x-forms.text fieldId="arrival_from" :fieldLabel="__('app.arrivalFrom')" fieldName="arrival_from" fieldRequired="true"
              :fieldPlaceholder="__('placeholders.city')">
            </x-forms.text>
          </div>
          <div class="col-md-3">
            <x-forms.label class="my-3" fieldId="booking_type_id" :fieldLabel="__('app.menu.bookingtype')" fieldRequired="true">
            </x-forms.label>
            <x-forms.input-group>
              <select class="form-control select-picker" name="booking_type_id" id="booking_type_id"
                data-live-search="true">
                <option value="">--</option>
                @foreach ($bookingtypes as $bookingtype)
                  <option value="{{ $bookingtype->id }}">
                    {{ $bookingtype->name }}
                  </option>
                @endforeach
              </select>
            </x-forms.input-group>
          </div>
        </div>
        <div class="row px-4">
          <div class="col-md-3">
            <x-forms.label class="my-3" fieldId="booking_reference_id" :fieldLabel="__('app.menu.hmbookingsource')" fieldRequired="true">
            </x-forms.label>
            <x-forms.input-group>
              <select class="form-control select-picker" name="booking_reference_id" id="booking_reference_id"
                data-live-search="true">
                <option value="">--</option>
                @foreach ($bookingsources as $bookingsource)
                  <option value="{{ $bookingsource->id }}">
                    {{ $bookingsource->name }}
                  </option>
                @endforeach
              </select>
            </x-forms.input-group>
          </div>
          <div class="col-md-3">
            <x-forms.text fieldId="booking_reference_no" :fieldLabel="__('app.bookingReferenceNo')" fieldName="booking_reference_no"
              fieldRequired="true" fieldPlaceholder="123456">
            </x-forms.text>
          </div>
          <div class="col-md-3">
            <x-forms.text fieldId="purpose_of_visit" :fieldLabel="__('app.purposeOfVisit')" fieldName="purpose_of_visit"
              fieldRequired="true" :fieldPlaceholder="__('placeholders.purposeOfVisit')">
            </x-forms.text>
          </div>
          <input type="hidden" name="company_id" value="{{ $company_id }}">
          <div class="col-md-3">
            <x-forms.textarea fieldId="remarks" :fieldLabel="__('app.remarks')" fieldName="remarks" fieldRequired="false"
              fieldPlaceholder="Write Remarks">
            </x-forms.textarea>
          </div>
        </div>
      </div>
      <div class="add-client bg-white rounded mt-4">
        <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
          @lang('app.RoomDetails')</h4>
        <div class="mt-3" id="rooms-container"></div>
        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-primary rounded-circle shadow-sm" id="add-room">
            <i class="fas fa-plus"></i>
          </button>
        </div>
        <x-form-actions>
          <x-forms.button-primary id="save-checkin-form" class="mr-3"
            icon="check">@lang('app.save')</x-forms.button-primary>
          <x-forms.button-cancel :link="route('hm-checkin.index')" class="border-0">@lang('app.cancel')</x-forms.button-cancel>
        </x-form-actions>
      </div>

    </x-form>
  </div>
</div>

<!-- Room Template -->
<div id="room-template" style="display: none;">
  <div class="card mb-3 room-item shadow-sm mx-3">
    <div class="card-header" style="background-color:#ED4040">
      <div class="d-flex justify-content-between align-items-center p-3 text-white">
        <h5>Room <span class="room-number"></span></h5>
        <button type="button" class="btn btn-danger btn-sm float-end remove-room">Remove</button>
      </div>
    </div>
    <div class="card-body room-form">
      <div class="row px-4">
        <div class="col-md-3">
          <x-forms.label class="my-3" fieldId="room_type_id" :fieldLabel="__('app.menu.roomtype')" fieldRequired="true">
          </x-forms.label>
          <x-forms.input-group>
            <select class="form-control roomtypes" name="room_type_id[]" id="hmroom_room_type_id">
              <option value="">--</option>
              @foreach ($roomtypes as $roomtype)
                <option value="{{ $roomtype->id }}">
                  {{ $roomtype->room_type_name }}
                </option>
              @endforeach
            </select>
          </x-forms.input-group>
        </div>
        <div class="col-md-3">
          <x-forms.label class="my-3" fieldId="room_id" :fieldLabel="__('app.menu.roomNo')" fieldRequired="true">
          </x-forms.label>
          <x-forms.input-group>
            <select class="form-control roomid roomsid" name="room_id[]" id="room_id">
              <option value="">--</option>
              @foreach ($roomnos as $roomno)
                <option value="{{ $roomno->id }}">
                  {{ $roomno->room_no }}
                </option>
              @endforeach
            </select>
          </x-forms.input-group>
        </div>

        <div class="col-md-3">
          <x-forms.text fieldId="adults" :fieldLabel="__('app.adults')" fieldName="adults[]" fieldRequired="false"
            fieldPlaceholder="Enter number of adults">
          </x-forms.text>
        </div>

        <div class="col-md-3">
          <x-forms.text fieldId="children" :fieldLabel="__('app.children')" fieldName="children[]" fieldRequired="false"
            fieldPlaceholder="Enter number of children">
          </x-forms.text>
        </div>
      </div>

      <div class="row px-4">
        <div class="col-md-6">
          <h5 class="mb-3">Total</h5>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Extra Bed</th>
                <th>Total Person</th>
                <th>Total Child</th>
                <th>Total Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="text" name="extra_bed[]" class="form-control" placeholder="Enter Extra Beds">
                </td>
                <td><input type="text" name="total_person[]" class="form-control" placeholder="Total Persons"
                    readonly></td>
                <!-- Total Person -->
                <td><input type="text" name="total_child[]" class="form-control" placeholder="Total Childrens"
                    readonly></td>
                <td><input type="text" name="total_amount[]" class="form-control" placeholder="Total Amount"
                    readonly></td> <!-- Total Amount -->
              </tr>
            </tbody>
          </table>
        </div>

        <div class="col-md-6">
          <h5 class="mb-3">Rent Info</h5>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Rent</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <input type="text" id="checkin" name="checkin[]" class="form-control datepicker"
                    placeholder="{{ __('placeholders.date') }}"
                    value="{{ now(company()->timezone)->translatedFormat(company()->date_format) }}">
                </td>
                <td>
                  <input type="text" id="checkout" name="checkout[]" class="form-control datepicker"
                    placeholder="{{ __('placeholders.date') }}"
                    value="{{ now(company()->timezone)->translatedFormat(company()->date_format) }}">
                </td>
                <td><input type="text" name="rent[]" class="form-control" placeholder="Enter Rent"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="guests-container mt-4">
        <div class="row px-4">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-5">
                <h5 class="mb-3">Guest Info</h5>
              </div>
              <div class="col-md-4"></div>
              <div class="col-md-3">
                <x-forms.input-group>
                  <select class="form-control select-picker guest-selects" id="guest_id_##roomIndex##"
                    data-roomid="##roomIndex##" data-live-search="true">
                    <option value="">-- Select Guest --</option>
                    @foreach ($guests as $guest)
                      <option value="{{ $guest->id }}" data-name="{{ $guest->first_name }}"
                        data-phone="{{ $guest->phone }}" data-image="{{ asset($guest->image_url) }}">
                        {{ $guest->first_name }}
                      </option>
                    @endforeach
                  </select>
                  <x-slot name="append">
                    <x-forms.link-secondary :link="route('hm-guests.create')" class="openRightModal btn btn-outline-secondary">
                      @lang('app.add')
                    </x-forms.link-secondary>
                  </x-slot>
                </x-forms.input-group>
              </div>
            </div>

            <div class="mt-4">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>SL</th>
                    <th>Name</th>
                    <th>Mobile No.</th>
                    <th>Id photo</th>
                    <th style="width: 100px;">Action</th>
                  </tr>
                </thead>
                <tbody class="guest-table-body" id="room-##roomIndex##-guest-table">
                </tbody>
              </table>
            </div>
            <div class="guest-hidden-input"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
  $(document).ready(function() {
    let roomCounter = 0;

    // Initialize datepicker
    $(document).on('focus', '.datepicker', function() {
      datepicker(this, {
        position: 'bl',
        ...datepickerConfig
      });
    });

    // Add Room
    $('#add-room').click(function() {
      const roomTemplate = $('#room-template').html().replace(/##roomIndex##/g, roomCounter);
      const $newRoom = $(roomTemplate).appendTo('#rooms-container');
      $newRoom.attr('data-room-index', roomCounter);

      const selectBookingTypePickerOptions = {
        liveSearch: true,
        title: '{{ __('app.menu.bookingtype') }}',
        noneSelectedText: '{{ __('app.pleaseSelect') }}'
      };

      $newRoom.find('select.roomtypes').html(`
        <option value="">--</option>
        @foreach ($roomtypes as $roomtype)
          <option value="{{ $roomtype->id }}">
            {{ $roomtype->room_type_name }}
          </option>
        @endforeach
      `).selectpicker({
        liveSearch: true,
        title: '{{ __('app.menu.bookingtype') }}',
        noneSelectedText: '{{ __('app.pleaseSelect') }}'
      });

      $newRoom.find('select.roomsid').html(`
        <option value="">--</option>
         @foreach ($roomnos as $roomno)
          <option value="{{ $roomno->id }}">
            {{ $roomno->room_no }}
          </option>
        @endforeach
      `).selectpicker({
        liveSearch: true,
        title: '{{ __('app.menu.hmbookingsource') }}',
        noneSelectedText: '{{ __('app.pleaseSelect') }}'
      });

      $newRoom.find('select.guest-selects').html(`
        <option value="">-- Select Guest --</option>
        @foreach ($guests as $guest)
            <option value="{{ $guest->id }}" data-name="{{ $guest->first_name }}"
            data-phone="{{ $guest->phone }}" data-image="{{ asset($guest->image_url) }}">
            {{ $guest->first_name }}
            </option>
        @endforeach
      `).selectpicker({
        liveSearch: true,
        title: 'Guest',
        noneSelectedText: '-- Select Guest --'
      })
      roomCounter++;
    });

    // Add Guest
    $(document).on('changed.bs.select', '.guest-selects', function() {
      let selectedGuest = $(this).find(":selected"); // Get selected option
      let guestId = selectedGuest.val();
      let guestName = selectedGuest.data("name");
      let guestPhone = selectedGuest.data("phone");
      let guestImage = selectedGuest.data("image");
      let gid = $(this).data("guestid");
      let roomId = $(this).data("roomid"); // Get the room ID from the data-roomid attribute

      if (!guestId) return;

      // Check if guestId already exists in the specific room's table
      if ($(`#room-${roomId}-guest-table tr[data-guestid='${guestId}']`).length > 0) {
        toastr.warning('This guest is already added to the list for this room.', 'Warning');
        return;
      }

      // Append new guest to the specific room's guest table
      let newRow = `
        <tr data-id="${guestId}" data-guestid="${guestId}" data-roomid="${roomId}">
            <td>${$(`#room-${roomId}-guest-table tr`).length + 1}
                <input type="hidden" name="guest_id[${roomId}][]" value="${guestId}">
                </td>
            <td>${guestName}</td>
            <td>${guestPhone}</td>
            <td>
                <a href="${guestImage}" download class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download"></i>
                </a>
            </td>
            <td>
                <a href="javascript:void(0);" class="remove-guest" data-id="${guestId}">
                    <i class="fas fa-trash-alt text-danger"></i>
                </a>
            </td>
        </tr>
    `;
      // Append new guest row to the room-specific guest table
      $(`#room-${roomId}-guest-table`).append(newRow);
      let roomForm = $(`.room-form[data-roomid="${roomId}"]`);
      let hiddenInput =
        `<input type="hidden" name="rooms[${roomId}][guests][]" value="${guestId}" data-guestid="${guestId}" data-roomid="${roomId}">`;
      roomForm.append(hiddenInput);
      // Disable selected guest in dropdown
      // selectedGuest.prop("disabled", true);
      // $(this).val(""); // Reset dropdown
      $(this).val("").selectpicker('refresh');
      // Show success toast
    });
    $(document).on('click', '.remove-guest', function() {
      let guestId = $(this).data('id'); // Get guest ID
      let roomForm = $(this).closest('.room-form'); // Get room form container

      // Remove the guest row from the table
      $(this).closest('tr').remove();

      // Enable the removed guest in the dropdown list
      $(`.guest-selects[data-roomid="${roomForm.data('roomid')}"] option[value="${guestId}"]`)
        .prop('disabled', false);

      // Refresh select picker and update row numbers
      $('.guest-selects').selectpicker('refresh');
      updateTableIndexes(roomForm);
    });

    // Remove Room
    $(document).on('click', '.remove-room', function() {
      $(this).closest('.room-item').remove();
      // Note: Removing rooms may affect index order in form submission
    });


    $('#checkin, #checkout, #check_in, #check_out, .checkout, .checkin').each(function(ind, el) {
      datepicker(el, {
        position: 'bl',
        ...datepickerConfig
      });
    });

    $('#add-room').trigger('click');

    $('#save-checkin-form').click(function() {

      const url = "{{ route('hm-checkin.store') }}";
      var data = $('#save-checkin-data-form').serialize();
      saveGuest(data, url, "#save-checkin-form");

    });

    function saveGuest(data, url, buttonSelector) {
      $.easyAjax({
        url: url,
        container: '#save-checkin-data-form',
        type: "POST",
        disableButton: true,
        blockUI: true,
        buttonSelector: buttonSelector,
        file: true,
        data: data,
        success: function(response) {
          if (response.status == 'success') {
            if ($(MODAL_XL).hasClass('show')) {
              $(MODAL_XL).modal('hide');
              window.location.reload();
            } else if (response.add_more == true) {

              var right_modal_content = $.trim($(RIGHT_MODAL_CONTENT).html());

              if (right_modal_content.length) {

                $(RIGHT_MODAL_CONTENT).html(response.html.html);
                $('#add_more').val(false);
              } else {

                $('.content-wrapper').html(response.html.html);
                init('.content-wrapper');
                $('#add_more').val(false);
              }

            } else {

              window.location.href = response.redirectUrl;

            }

            if (typeof showTable !== 'undefined' && typeof showTable === 'function') {
              showTable();
            }

          }

        }
      });
    }
    $(document).on('input', 'input[name="adults[]"]', function() {
      let row = $(this).closest('.room-form');
      let adults = parseInt($(this).val()) || 0;

      row.find('input[name="total_person[]"]').val(adults);
      calculateTotalAmount(row);
    });

    $(document).on('input', 'input[name="children[]"]', function() {
      let row = $(this).closest('.room-form');
      let children = parseInt($(this).val()) || 0;

      row.find('input[name="total_child[]"]').val(children);
      calculateTotalAmount(row);
    });

    $(document).on('input',
      'input[name="extra_bed[]"], input[name="total_person[]"], input[name="total_child[]"]',
      function() {
        let row = $(this).closest('.room-form');
        calculateTotalAmount(row);
      });

    function calculateTotalAmount(row) {
      let extraBed = parseInt(row.find('input[name="extra_bed[]"]').val()) || 0;
      let totalPerson = parseInt(row.find('input[name="total_person[]"]').val()) || 0;
      let totalChild = parseInt(row.find('input[name="total_child[]"]').val()) || 0;

      let extraBedCost = extraBed * 100;
      let totalPersonCost = totalPerson * 300;
      let totalChildCost = totalChild * 150;

      let totalAmount = extraBedCost + totalPersonCost + totalChildCost;

      row.find('input[name="total_amount[]"]').val(totalAmount);
    }
  });
</script>
