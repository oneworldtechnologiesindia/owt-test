<!-- JAVASCRIPT -->
<script src="{{ URL::asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/bootstrap/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/metismenu/metismenu.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/node-waves/node-waves.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/toastr/toastr.min.js') }}"></script>

<script src="https://unpkg.com/autonumeric@4.8.3/dist/autoNumeric.min.js"></script>
{{-- <script src="https://www.jqueryscript.net/demo/Easy-Numbers-Currency-Formatting-Plugin-autoNumeric/index_files/autoNumeric-1.8.3.js"></script> --}}
<script src="{{ URL::asset('assets/libs/jquery-number-master/jquery.number.js') }}"></script>
<script>
    $('.change-password-modalbtn').on('click', function(event) {
        event.preventDefault();
        $('.change-password').modal('show');
        $('.change-password form')[0].reset();
        $('.change-password form').find('input.is-invalid').removeClass('is-invalid');
        $('.change-password form').find('.invalid-feedback').text('');
    })
    $('#change-password').on('submit', function(event) {
        event.preventDefault();
        var Id = $('#data_id').val();
        var current_password = $('#current-password').val();
        var password = $('#spassword').val();
        var password_confirm = $('#spassword-confirm').val();
        $('#scurrent_passwordError').text('');
        $('#spasswordError').text('');
        $('#password_confirmError').text('');
        $.ajax({
            url: "{{ route('passwords.update') }}",
            type: "POST",
            data: {
                "id": Id,
                "current_password": current_password,
                "password": password,
                "password_confirmation": password_confirm,
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                $('#scurrent_passwordError').text('');
                $('#spasswordError').text('');
                $('#password_confirmError').text('');
                if (response.status == false) {
                    showMessage('error', response.message);
                } else if (response.status == true) {
                    showMessage('success', response.message);
                    $('.change-password').modal('hide');
                }
            },
            error: function(response) {
                if (response.responseJSON.errors.current_password) {
                    $('#current_passwordError').html('<strong>' + response.responseJSON
                        .errors.current_password + '</strong>');
                    $('#current-password').addClass('is-invalid');
                }
                if (response.responseJSON.errors.password) {
                    $('#spasswordError').html('<strong>' + response.responseJSON.errors.password +
                        '</strong>');
                    $('#spassword').addClass('is-invalid');
                }
                if (response.responseJSON.errors.password_confirmation) {
                    $('#spassword_confirmError').html('<strong>' + response.responseJSON.errors
                        .password_confirmation + '</strong>');
                    $('#spassword-confirm').addClass('is-invalid');
                }
            }
        });
    });
</script>
@if (Session::has('status'))
    <script type="text/javascript">
        $(document).ready(function() {
            showMessage("success", "{{ Session::get('status') }}");
        })
    </script>
    @php Session::forget('status') @endphp
@endif
@if (Session::has('success'))
    <script type="text/javascript">
        $(document).ready(function() {
            showMessage("success", "{{ Session::get('success') }}");
        })
    </script>
    @php Session::forget('success') @endphp
@endif
@if (Session::has('error'))
    <script type="text/javascript">
        $(document).ready(function() {
            showMessage("error", "{{ Session::get('error') }}");
        })
    </script>
    @php Session::forget('error') @endphp
@endif
@if (Session::has('warning'))
    <script type="text/javascript">
        $(document).ready(function() {
            showMessage("warning", "{{ Session::get('warning') }}");
        })
    </script>
    @php Session::forget('warning') @endphp
@endif
@yield('script')

<!-- App js -->
<script src="{{ URL::asset('assets/js/app.min.js') }}"></script>

<!-- Custom js -->
<script src="{{ URL::asset('assets/js/custom.js?v=5001') }}"></script>

@yield('script-bottom')
