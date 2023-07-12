<style>
    #example-two-button {
        width: 100%;
        text-align: right;
        background: transparent;
        color: black;
    }
</style>


<div class="modal fade" id="compineModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.compine')}}</label>
                <button type="button" class="close modal-close-btn close-create" data-bs-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form method="POST" action="{{ route('compineItem') }}"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.compine_item') }} <span
                                        style="color:red; font-size:20px; font-weight:bold;">*</span></label>
                                <input type="text" id="parent_name" name="parent_name" required
                                       class="form-control" readonly
                                       value="{{Config::get('app.locale') == 'ar' ?  $item -> name_ar : $item -> name_en}}"/>
                                <input type="hidden" id="parent_id" name="parent_id" value="{{  $item -> id }}">

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.karat') }} <span
                                        style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text" id="karat" name="karat"
                                       class="form-control" readonly
                                       value="{{Config::get('app.locale') == 'ar' ? $item -> karat -> name_ar : $item -> karat -> name_en}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.items') }} <span
                                        style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <div class="dropdown hierarchy-select" id="example">
                                    <button type="button" class="btn btn-secondary dropdown-toggle"
                                            id="example-two-button" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="example-two-button" style="width: 100%;
text-align: center;">
                                        <div class="hs-searchbox">
                                            <input type="text" class="form-control" autocomplete="off">
                                        </div>
                                        <div class="hs-menu-inner">
                                            @foreach($items as $item)
                                                <a class="dropdown-item" data-value="{{$loop -> index + 1}}" href="#"
                                                   onclick="selectItem({{$item -> id}})">
                                                    {{Config::get('app.locale') == 'ar' ? $item -> name_ar . '--' . $item -> code : $item -> name_en  . '--' . $item -> code}}</a>
                                            @endforeach
                                            <input hidden required id="item_id" name="item_id" value="0">

                                        </div>
                                    </div>
                                    <input class="d-none" name="example_two" readonly="readonly" aria-hidden="true"
                                           type="text"/>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-6" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="submit" class="btn btn-labeled btn-primary">
                                {{__('main.save_btn')}}</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.no_metal')}} </th>
                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.price')}} </th>
                            <th class="text-end text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $item)
                            <tr>
                                <td class="text-center">{{$loop -> index + 1}}</td>
                                <td class="text-center">{{$item -> code}}</td>
                                <td class="text-center">{{$item -> name_ar}}</td>
                                <td class="text-center">{{$item -> name_en}}</td>
                                <td class="text-center">{{Config::get('app.locale') == 'ar' ? $item -> category_name_ar : $item -> category_name_en }}</td>
                                <td class="text-center">{{Config::get('app.locale') == 'ar' ? $item -> karat_name_ar : $item -> karat_name_en}}</td>
                                <td class="text-center">{{$item -> weight}}</td>
                                <td class="text-center">{{$item -> no_metal}}</td>
                                <td class="text-center">{{$item -> price == 0 ? '--' : $item -> price}}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-labeled btn-danger deleteCombineBtn "
                                            value="{{$item -> id}}">
                                                    <span class="btn-label" style="margin-right: 10px;"><i
                                                            class="fa fa-trash"></i></span>{{__('main.delete')}}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>

<!-- Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha256-CjSoeELFOcH0/uxWu6mC/Vlrc1AARqbm/jiiImDGV3s=" crossorigin="anonymous"></script>

<!-- Bootstrap CSS -->
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css'>
<script src=" {{asset('assets/js/hierarchy-select.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#example').hierarchySelect({
            hierarchy: false,
            width: 'auto'
        });
    });

    function selectItem(id) {
        document.getElementById('item_id').value = id;
        console.log(document.getElementById('item_id').value);
    }
</script>
