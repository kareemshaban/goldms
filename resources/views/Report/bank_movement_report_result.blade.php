<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gold MS</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Custom styles for this template-->
    <link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{asset('assets/css/sb-admin-2.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/printA4Portrait.css')}}" rel="stylesheet">

</head>

<body id="page-top" @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    @include('layouts.side' , ['slag' => 14 , 'subSlag' => 1441])
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            @include('layouts.header')
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                @include('flash-message')
                <div class="d-sm-flex align-items-center justify-content-between mb-4" style="padding: 8px">

                    <h1 class="h3 mb-0 text-primary-800 no-print">{{__('main.reports')}} / {{__('main.bank_movement_report')}}</h1>
                    <button type="button" class="btn btn-info no-print" id="btnPrint">Print</button>
                </div>

                <div class="card-body px-0 pt-0 pb-2">

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 " style="border:solid 1px gray">
                            <header>
                                    <div class="row" style="direction: ltr;">
                                        <div class="col-4 c">
                                            <span style="text-align: left; font-size:15px;">{{$company ? $company -> name_en : ''}}

                                        <br> C.R :   {{$company ? $company -> registrationNumber : ''}}
                                       <br>  Vat No :   {{$company ? $company -> taxNumber : ''}}
                                      <br>  Tel :   {{ $company ? $company -> phone : ''}}

                                   </span>
                                        </div>
                                        <div class="col-4 c">
                                            <label style="text-align: center; font-weight: bold"> تقرير كشف حركة بنك</label>
                                        </div>
                                        <div class="col-4 c">
                                       <span style="text-align: right;">{{$company ? $company -> name_ar : ''}}

                                        <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                                       <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                                      <br>  تليفون :   {{$company ? $company -> phone : ''}}
                                       </span>
                                        </div>
                                    </div>

                            </header>

                        </div>

                        <div class="card-body">
                            <h4 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h4>

                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">{{__('main.date')}}</th>
                                        <th class="text-center">{{__('main.basedon_no')}}</th>
                                        <th class="text-center">{{__('main.document_type')}}</th>
                                        <th class="text-center">{{__('main.Debit')}}</th>
                                        <th class="text-center">{{__('main.Credit')}}</th>
                                        <th class="text-center">{{__('main.balance')}}</th>


                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php  $net = 0  ?>
                                    @foreach($holders as $holder )
                                        <tr>
                                            <td class="text-center">{{$loop -> index + 1}}</td>
                                            <td class="text-center">{{\Carbon\Carbon::parse($holder -> date) -> format('d-m-Y')}}</td>
                                            <td class="text-center">{{$holder -> docNumber}}</td>
                                            <td class="text-center">{{$holder -> docType}}</td>
                                            <td class="text-center">{{$holder -> debit}}</td>
                                            <td class="text-center">{{$holder -> credit}}</td>
                                            <td class="text-center">{{$holder -> credit - $holder -> debit}}</td>

                                            <?php  $net += ($holder -> credit - $holder -> debit)  ?>
                                        </tr>


                                    @endforeach
                                    <tr>
                                        <td class="text-center" colspan="6">{{__('main.total_balance')}}</td>
                                        <td class="text-center" >{{$net}}</td>

                                    </tr>


                                    </tbody>

                                </table>


                            </div>




                        </div>
                    </div>

                </div>


            </div>
        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        @include('layouts.footer')
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>


<script type="text/javascript">
    let id = 0;


    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            printPage();

        });

    });


    function printPage() {
        var css = '@page { size: landscape; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);
        window.print();
    }


</script>





<script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<!-- Core plugin JavaScript-->
<script src="{{asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>


<script src="{{asset('assets/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('assets/js/demo/datatables-demo.js')}}"></script>

</body>

</html>
