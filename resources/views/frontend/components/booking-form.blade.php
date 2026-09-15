 <form id="registerForm" action="{{ route('book.ticket') }}" method="post">
     @csrf

     @if ($errors->has('registration_closed'))
         <div class="alert alert-danger my-2">
             {{ $errors->first('registration_closed') }}
         </div>
     @endif

     <div class="mb-3">
         <label for="">Your Name<span class="text-danger">*</label>
         <input type="text" placeholder="Full name" name="name" class="form-control" id=""
             value="{{ old('name') }}">
         @error('name')
             <span class="text-danger">{{ $message }}</span>
         @enderror
     </div>
     <div class="mb-3">
         <label for="">Email<span class="text-danger">*</label>
         <input type="email" placeholder="Email id" name="email" class="form-control" id=""
             value="{{ old('email') }}">
         @error('email')
             <span class="text-danger">{{ $message }}</span>
         @enderror
     </div>
     <div class="mb-3">
         <label for="">Phone<span class="text-danger">*</label> <small>only 10 Digits no space</small>
         <input type="tel" placeholder="Mobile no." name="phone" class="form-control" id=""
             value="{{ old('phone') }}">
         @error('phone')
             <span class="text-danger">{{ $message }}</span>
         @enderror
     </div>
     <div class="row">
         <div class="col-md-6">
             <div class="mb-3">
                 <label for="">Organization<span class="text-danger"></label>
                 <input type="text" placeholder="Company name" name="company" class="form-control" id=""
                     value="{{ old('company') }}">
                 @error('company')
                     <span class="text-danger">{{ $message }}</span>
                 @enderror
             </div>
         </div>
         <div class="col-md-6">
             <div class="mb-3">
                 <label for="mb-0" for="">Designation<span class="text-danger"></label>
                 <input type="text" placeholder="Designation" name="designation" class="form-control" id=""
                     value="{{ old('designation') }}">
                 @error('designation')
                     <span class="text-danger">{{ $message }}</span>
                 @enderror
             </div>
         </div>
     </div>
     <div class="row">
         <div class="col-md-12">
             <div class="mb-3">
                 <label for="mb-0">Event</label>
                 
                  @php
    if (!isset($event)) {
        $event = (object)[
            'title' => null,
            'date'  => null,
        ];
    }

    $date = $event->date; // gives $date a default too
@endphp
                 <select name="event" class="form-select w-100 border py-2 rounded"
                     aria-label="Default select example" required>
                    <option value="">Select an Event</option>  
                     @foreach ($upcomingEvents as $event)
                         <option value="{{ $event->title }}" {{ old('event') == $event->title ? 'selected' : '' }}>
                             {{ $event->title }}
                         </option>
                     @endforeach
                 </select>
                 @error('event')
                     <span class="text-danger">{{ $message }}</span>
                 @enderror
             </div>
         </div>
     </div>

     <input type="hidden" name="date" value="{{ $event->date }}">

     <div class="mb-3">
         <button id="submitBtn" type="submit" class="btn btn-primary ">Register Now</button>
     </div>


 </form>


 <script>
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <i class="fa fa-spinner fa-spin me-2"></i>
            Registering...
        `;
    });
</script>

