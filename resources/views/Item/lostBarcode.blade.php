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
    <link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{asset('assets/css/sb-admin-2.css')}}" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Libre Barcode 128 Text' rel='stylesheet'>



</head>

<body id="page-top" @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    @include('layouts.side' , ['slag' => 3 , 'subSlag' => 43])
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
                    <h1 class="h3 mb-0 text-primary-800">{{__('main.basic_data')}} / {{__('main.lost_barcode')}}</h1>

                </div>

                <div class="card-header">
                   <div class="form-group">
                      <label class="form-label">{{__('main.weight')}}</label>
                       <input class="form-control" id="weight" name="weight" type="number" step="any">
                   </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12" style="display: block; margin: 20px auto; text-align: center;">
                                <button type="button" class="btn btn-labeled btn-primary"  onclick="searchByweight()" style="width: 50%">
                                    {{__('main.search_btn')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">{{__('main.item_list')}}</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered"  width="100%" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">
                                            #
                                        </th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.code')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.name_ar')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.name_en')}} </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.category')}} </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.karat')}} </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.weight')}} </th>
                                        <th class="text-end text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbody">

                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
            <!-- /.container-fluid -->

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









<div class="show_modal">

</div>

<div class="barcode_modal">

</div>
<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#profile-img-tag').attr('src', e.target.result);

            }
            reader.readAsDataURL(input.files[0]);
            document.getElementById('path').innerHTML = input.files[0].name;
        }
    }

    $("#img").change(function () {
        readURL(this);
    });

</script>

<script type="text/javascript">
    let id = 0;


    $(document).ready(function () {
        id = 0;
        $(document).on('click', '.cancel-modal', function (event) {
            $('#deleteModal').modal("hide");
            id = 0;
        });
        $(document).on('click', '.close-create', function (event) {
            $('#createModal').modal("hide");
            id = 0;
        });

    });


    function searchByweight(){
        var weight = 0 ;
        weight = document.getElementById('weight').value ;
        $.ajax({
            type: 'get',
            url: 'lost_barcode_search' + '/' + weight,
            dataType: 'json',

            success: function (response) {
                console.log(response);
                if (response) {


                    $("#tbody").empty();
                    for(let i = 0 ; i < response.length ; i++){
                        var newTr = $('<tr data-item-id="'+response.id+'">');

                        var tr_html = '<td class="text-center">  ' + (i + 1) +'   </td>' ;
                        tr_html += '<td class="text-center">  ' + response[i].code + '   </td>';
                        tr_html += '<td class="text-center">  ' + response[i].name_ar + '   </td>';
                        tr_html += '<td class="text-center">  ' + response[i].name_en + '   </td>';
                        tr_html += '<td class="text-center">  ' + response[i].category_name_ar + '   </td>';
                        tr_html += '<td class="text-center">  ' + response[i].karat_name_ar + '   </td>';
                        tr_html += '<td class="text-center">  ' + response[i].weight + '   </td>';

                        var route = '{{route('printBarcode',":id")}}';
                        route = route.replace(":id",response[i].id);
                        tr_html += `<td>  <a href = ${route}   target="_blank" >
                            <button type="button" class="btn btn-labeled btn-warning printBTN" >
                            <span class="btn-label" style="margin-right: 10px;"><i
                        class="fa fa-barcode" style="margin-left: 5px;
                        margin-right: 5px;"></i></span>{{__('main.print_barcode')}}
                            </button>
                        </a> </td>`;

                        newTr.html(tr_html);
                        newTr.appendTo('#tbody');

                    }



                   } else {

                }
            }
        });
    }

</script>

<!-- Bootstrap core JavaScript-->
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
