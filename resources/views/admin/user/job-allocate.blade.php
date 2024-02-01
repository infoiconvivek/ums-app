@extends('layouts.admin_layout')
@section('content')
@section('title', 'Job')


<!-- ========== App Menu ========== -->

<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->

            <!-- end page title -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Job Booking</h4>

                        </div><!-- end card header -->

                        <div class="card-body">
                            @if (session('msg'))
                            <div class="alert alert-{{ session('msg_type') }}" role="alert">
                                {{ session('msg') }}
                            </div>
                            @endif

                            <form method="post" action="{{url('admin/user/job-booking')}}" enctype="multipart/form-data">
                                @csrf

                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#job" role="tab" aria-selected="false" tabindex="-1">
                                            Booking
                                        </a>
                                    </li>

                                  

                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content  text-muted">

                                    <div class="tab-pane active" id="job" role="tabpanel">
                                        <div class="row gy-4">
                                           

                                            <div class="col-xxl-6 col-md-6">
                                                <div>
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" class="form-control" name="name" value="{{ isset($user)?$user->first_name.' '.$user->last_name :old('name') }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-xxl-6 col-md-6">
                                                <div>
                                                    <label for="email" class="form-label">Email</label>
                                                    <input type="text" class="form-control" name="email" value="{{ isset($user)?$user->email:old('email') }}" readonly>
                                                </div>
                                            </div>


                                            <div class="col-xxl-6 col-md-6">
                                                <div>
                                                    <label for="phone" class="form-label">Phone</label>
                                                    <input type="text" class="form-control" name="phone" value="{{ isset($user)?$user->phone:old('phone') }}" readonly>
                                                </div>
                                            </div>
                                            
                                              <div class="col-xxl-6 col-md-6">
                                                <div>
                                                    <label for="job_id" class="form-label">Job</label>
                                                    <select  class="form-control" name="job_id">
                                                    <option value=""> --Select Job-- </option>
                                                        @foreach($jobs as $job)
                                                     <option value="{{$job->id}}"> {{$job->title}} - {{$job->date}} </option>
                                                       @endforeach
                                                    </select>
                                                </div>
                                                @error('job_id')
                                                <span class="text-danger">{{$message}}</span>
                                                @enderror
                                            </div>





                                            <!--end col-->

                                            <!--end col-->
                                        </div>
                                    </div>





                                </div>
                                  <br>
                                <div class="row gy-4">
                                <div class="col-xxl-4 col-md-4">
                                 <input type="hidden" name="user_id" value="{{ isset($user)?$user->id:old('user_id') }}">
                                  <input type="submit" class="btn btn-info" value="Book Now">
                                </div>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->




        </div> <!-- container-fluid -->
    </div><!-- End Page-content -->

    @include('includes.admin.footer')
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->

<script>
CKEDITOR.replace( 'descriptions' );
</script>

<script>
CKEDITOR.replace( 'desc' );
</script>

@stop