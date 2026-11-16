{{-- Behaviour for _service_fields (inside $(document).ready). --}}
    function serviceType() {
        return $('input[name="service_type"]:checked').val();
    }

    // Mileage is required for oil changes, optional for periodic maintenance and repairs
    function toggleMileageSection() {
        var type = serviceType();
        $('.service-type-card').removeClass('active');
        $('input[name="service_type"]:checked').closest('.service-type-card').addClass('active');

        var required = type === 'oil_change';
        $('#serviced_kilometer, #next_service_kilometer').prop('required', required);
        $('.km-required').toggle(required);
        $('.km-optional').toggle(!required);
        calculateNextService();
    }

    function calculateNextService() {
        var currentKm = parseInt($('#serviced_kilometer').val());
        var intervalVal = $('#next_service_interval').val();

        if (intervalVal === 'custom') {
            $('#next_service_kilometer').prop('readonly', false);
        } else {
            $('#next_service_kilometer').prop('readonly', true);
        }

        if (isNaN(currentKm) || currentKm < 0) {
            if (intervalVal !== 'custom') $('#next_service_kilometer').val('');
            $('#calc-summary').text('Input the current mileage to calculate the next service.');
            return;
        }

        if (intervalVal === 'custom') {
            var nextKm = parseInt($('#next_service_kilometer').val());
            if (isNaN(nextKm)) {
                $('#calc-summary').text('Input the mileage for the next service.');
            } else {
                $('#calc-summary').html('Next service after <strong>' + (nextKm - currentKm).toLocaleString() + ' km</strong> (at <strong>' + nextKm.toLocaleString() + ' km</strong>).');
            }
        } else {
            var addKm = parseInt(intervalVal);
            var resultKm = currentKm + addKm;
            $('#next_service_kilometer').val(resultKm);
            $('#calc-summary').html('Next service: <strong>' + currentKm.toLocaleString() + ' km</strong> + <strong>' + addKm.toLocaleString() + ' km</strong> = <strong>' + resultKm.toLocaleString() + ' km</strong>.');
        }
    }

    function calculateNextDate() {
        var months = $('#next_service_months').val();
        if (months === '') { $('#next_service_date').val(''); return; }
        if (months === 'custom') { $('#next_service_date').trigger('focus'); return; }
        var base = new Date($('#service_date').val() || new Date());
        base.setMonth(base.getMonth() + parseInt(months));
        $('#next_service_date').val(base.toISOString().slice(0, 10));
    }

    $('input[name="service_type"]').on('change', toggleMileageSection);
    $('#serviced_kilometer, #next_service_interval').on('input change', calculateNextService);
    $('#next_service_kilometer').on('input', function () {
        if ($('#next_service_interval').val() === 'custom') calculateNextService();
    });
    $('#next_service_months').on('change', calculateNextDate);
    $('#service_date').on('change', function () {
        if (['3', '6', '12'].indexOf($('#next_service_months').val()) !== -1) calculateNextDate();
    });
    $('#next_service_date').on('input', function () { $('#next_service_months').val('custom'); });
    if ($('#next_service_date').val()) $('#next_service_months').val('custom');

    toggleMileageSection();
