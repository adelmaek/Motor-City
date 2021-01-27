@extends('layouts.app')



@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Transaction</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Query POS Account Transactions</h3>
                    <form action="{{route('queryPosAccountTransaction',[$accountId])}}" method="post">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fromDateInput">From Date</label>
                                    <input type="date" class="form-control" id="fromDateInput" name="fromDateInput" style="height: 42px;" >     
                                  </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fromDateInput">To Date</label>
                                    <input type="date" class="form-control" id="toDateInput" name="toDateInput" style="height: 42px;" value={{$today}} required>     
                                  </div>
                            </div>
                            {{-- @if (Auth::user()->admin)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="brandIdInput">Brand</label>
                                    <select class="form-control" style="height: 42px;" id="brandIdInput" name="brandIdInput" required>
                                        <option value="" disabled selected>Choose brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{$brand->id}}">{{$brand->name}}</option>
                                        @endforeach                                        
                                    </select>
                                </div>
                            </div>
                            @endif --}}
                        </div>
                        <input type="submit" name="submit" class="btn btn-dark btn-md" value="Submit">
                        <input type="hidden" name="_token" value="{{Session::token()}}">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Transactions</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Quiered Transactions</h3>
                    <form action="{{route('settlePosTransactions')}}" method="post">
                        <table  id="transactionsTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table " style="width:100%">
                            <thead style="width:100%">
                                <tr>
                                    <th scope="col" style="text-align:center">Settle</th>
                                    <th scope="col" style="text-align:center">Date</th>
                                    <th scope="col" style="text-align:center">Deposite</th>
                                    <th scope="col" style="text-align:center">Withdrawal</th>
                                    <th scope="col" style="text-align:center">Current Balance</th>
                                    <th scope="col" style="text-align:center">Description</th>
                                    <th scope="col" style="text-align:center">Client</th>
                                    <th scope="col" style="text-align:center">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $trans)
                                    <tr>
                                        <td style="text-align:center">
                                            @if($trans->settled)
                                                @if(!strcmp($trans->type,"add"))
                                                    <input type="checkbox"  class="form-check-input" id="{{$trans->id}}" name="settled[]"  placeholder="Value" value="{{$trans->id}}" disabled >
                                                @else
                                                    @if(!$trans->confirmSettling)
                                                        <a class="btn btn-success active" style="height:25px;padding: 3px 8px;padding-bottom: 3px;" onclick="confirmSettling({{$trans->id}})"  role="button">Confirm</a>
                                                    @else
                                                        <a class="btn btn-info disabled" style="height:25px;padding: 3px 8px;padding-bottom: 3px;" onclick="confirmSettling({{$trans->id}})"  role="button" >Confirm</a>
                                                    @endif
                                                 @endif
                                            @else
                                                <input type="checkbox"  class="form-check-input" id="{{$trans->id}}" name="settled[]"  placeholder="Value" value="{{$trans->id}}">
                                            @endif
                                        </td>
                                        <td style="text-align:center">{{$trans->date}}</td>
                                        @if(!strcmp($trans->type,"add"))
                                            <td style="text-align:center">{{number_format($trans->value)}}</td>
                                        @else
                                            <td style="text-align:center"> - </td>
                                        @endif
                                        @if(!strcmp($trans->type,"sub"))
                                            <td style="text-align:center">{{number_format($trans->value)}}</td>
                                        @else
                                            <td style="text-align:center"> - </td>
                                        @endif
                                        <td style="text-align:center">{{number_format($trans->currentBalance)}}</td>
                                        <td style="text-align:center">{{$trans->description}}</td>
                                        <td style="text-align:center">{{$trans->clientName}}</td>
                                        @if(\Carbon\Carbon::parse($trans->date)->gte(\Carbon\Carbon::parse($yesterday)) || Auth::user()->admin)
                                            <td style="text-align:center">
                                                <a class="btn btn-danger delete-confirm" style="height:25px;padding: 3px 8px;padding-bottom: 3px;" href="{{route('deleteTransaction',[$trans->id])}}" role="button">Delete</a>
                                            </td>
                                        @else 
                                            <td style="text-align:center">
                                                <a class="btn btn-danger delete-confirm disabled" style="height:25px;padding: 3px 8px;padding-bottom: 3px;" href="{{route('deleteTransaction',[$trans->id])}}" role="button">Delete</a>
                                            </td>
                                        @endif
                                    </tr>  
                                @endforeach   
                            </tbody>
                        </table>
                        <input type="submit" name="submit" class="btn btn-dark btn-md" value="Settle">
                        <input type="hidden" name="_token" value="{{Session::token()}}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Confirm Settling</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="{{ url('/') }}" method="post" id="modalForm">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="valueInput">Value</label>
                            <input type="number" step ="0.01" class="form-control" id="valueInput" name="valueInput" placeholder="Value" required style="min-width: 100px;border: 2px solid black;" >
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bankIdInput">Settling Bank</label>
                            <select class="form-control" style="min-width: 100px;border: 2px solid black;" id="bankIdInput" name="bankIdInput" >
                                <option value="" disabled selected>Settle to bank account</option>
                                @foreach ($bankAccounts as $bankAccount)
                                    <option value="{{$bankAccount->id}}">{{App\Models\Bank::where('id',$bankAccount->bankID)->first()->name}} {{$bankAccount->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div> --}}
                <input type="submit" name="submit" class="btn btn-dark btn-md" value="Settle">
                <input type="hidden" name="_token" value="{{Session::token()}}">
            </form>
        </div>
        </div>
    </div>
</div>
@endsection




@section('extraJS')
<script>
    $('#transactionsTable').DataTable({
        "displayLength": 5,
        "processing": true,
        dom: 'Bfrtip',
        buttons: [
                {
                extend: 'excel',
                title: 'Motor-City-Transactions',
                footer: true
            }
        ]   ,
        "scrollY":"390px",
        "sScrollX": "100%",
        responsive: true,
        "scrollCollapse": true,
        "paging":         false,
        "order": []
    });
    $(' .buttons-print,.buttons-excel').addClass('btn btn-primary mr-1');
</script>
<script>
    function confirmSettling(transId){
        $('#editModal').modal('toggle');
        var path = $("#modalForm").attr('action')+ "/confirmSettlingPos/" +transId;
        $("#modalForm").attr('action', path );
    };
</script>
@endsection