@extends('header_and_sidebar')

@section('styles')
@endsection

@section('content')
    {{-- <div class="container my-3">
        <div class="card">
            <div class="card-header">
                Financial Year Reports
            </div>
            <div class="card-body">
                
                <div class="table table-responsive my-3">
                 
                </div>
            </div>
        </div>
        <div class="card card-body my-3" id="bar_chart" style="width: 100%; height: 500px;">

        </div>
    </div> --}}
    <div class="row my-3">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/leads_reports" class="stretched-link"></a>
                    <p class="card-text" >Leads Report</p>
                   
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/equipment_report" class="stretched-link"></a>
                    <p class="card-text" >Equipment Report</p>
                    
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/vendor_product_report" class="stretched-link"></a>
                    <p class="card-text" >Vendor Product Report</p>
                   
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/customer_single_view_get" class="stretched-link"></a>
                    <p class="card-text" >Customer Single View   </p>
                 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="card  border-primary">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/mis_reports" class="stretched-link"></a>
                    <p class="card-text text-dark" >MIS Report</p>
                   
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/daybyday_report" class="stretched-link"></a>
                    <p class="card-text text-dark" >Day By Day Report</p>
                    
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/deliveryReportFilter/today/All" class="stretched-link"></a>
                    <p class="card-text text-dark" >Order Report</p>
                   
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/collectionReport" class="stretched-link"></a>
                    <p class="card-text text-dark" >Collection Report</p>
                 
                </div>
            </div>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-md-3">
            <div class="card  border-secondary">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/googleCampaignReport" class="stretched-link"></a>
                    <p class="card-text text-dark" >Campaign Report</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/timeline" class="stretched-link"></a>
                    <p class="card-text text-dark" >Timeline</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/fy_report" class="stretched-link"></a>
                    <p class="card-text text-dark" >Financial Year Report</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-muted">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/monthly_records" class="stretched-link"></a>
                    <p class="card-text text-dark" >Monthly Records</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-md-3">
            <div class="card  border-success">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/monthly_report" class="stretched-link"></a>
                    <p class="card-text text-dark" >Monthly Report</p>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <h5 class="text-dark">Delivery Management</h5>
    <div class="row my-3">
        <div class="col-md-3">
            <div class="card  border-primary">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/order_feedback" class="stretched-link"></a>
                    <p class="card-text text-dark" >Feedback</p>
                   
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <a href="#" class="stretched-link"></a>
                    <p class="card-text text-dark" >Deliver Order</p>
                    
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/ArchivedDeliveries" class="stretched-link"></a>
                    <p class="card-text text-dark" >Report Archived</p>
                   
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/CompletedDeliveries" class="stretched-link"></a>
                    <p class="card-text text-dark" >Completed</p>
                 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/AllDeliveries" class="stretched-link"></a>
                    <p class="card-text text-dark" >All Open</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <a href="#" class="stretched-link"></a>
                    <p class="card-text text-dark" >Report Assign Open Task</p>
                    
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <a href="{{url('/')}}/MonthlyDeliveryReport" class="stretched-link"></a>
                    <p class="card-text text-dark" >Date By Report</p>
                   
                </div>
            </div>
        </div>
      
    </div>
@endsection

@section('script')


@endsection