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

                            <form method="post" action="{{url('admin/job/booking')}}" enctype="multipart/form-data">
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
                                                    <label for="title" class="form-label">Job Status</label>
                                                    <input type="text" class="form-control" name="title" value="@if(isset($job) && $job->status == 1) Active @elseif (isset($job) && $job->status == 2) Booked @else Inactive @endif"   readonly>
                                                </div>
                                            </div>

                                            <div class="col-xxl-6 col-md-6">
                                                <div>
                                                    <label for="title" class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title" value="{{ isset($job)?$job->title:old('title') }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-xxl-6 col-md-6">
                                                <div>
                                                    <label for="title" class="form-label">Location</label>
                                                    <input type="text" class="form-control" name="location" value="{{ isset($job)?$job->location:old('location') }}" readonly>
                                                </div>
                                            </div>


                                            <div class="col-xxl-6 col-md-6">
                                                <div>
                                                    <label for="title" class="form-label">Date</label>
                                                    <input type="date" class="form-control" name="date" value="{{ isset($job)?$job->date:old('date') }}" readonly>
                                                </div>
                                            </div>
                                            
                                              <div class="col-xxl-6 col-md-6">
                                                <div>
                                                    <label for="phone" class="form-label">Phone No. (User)</label>
                                                    <input type="text" class="form-control" name="phone">
                                                </div>
                                                @error('phone')
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
                                 <input type="hidden" name="job_id" value="{{ isset($job)?$job->id:old('job_id') }}">
                                @if($job->status == 2)
                                 <a href="{{url('admin/job/booking-revoke?job_id='.$job->id)}}" class="btn btn-danger">Revoke Booking</a>
                                 @php
                                 $booking_id = App\Helpers\Helper::getBookingId($job->id);
                                 @endphp
                                 <a href="{{url('admin/booking/view/'.$booking_id)}}" class="btn btn-info"> Booking Details</a>
                                 @elseif($job->status == 1)
                                  <input type="submit" class="btn btn-info" value="Book Now">
                                @endif
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