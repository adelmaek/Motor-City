@extends('layouts.app')



@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Insert Transaction</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Insert Temp Transaction</h3>
                    <form action="{{route('tempTransactions')}}" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dateInput">To Date</label>
                                    <input type="date" class="form-control" id="dateInput" name="dateInput" style="height: 42px;border: 2px solid black;" value={{$today}} required>     
                                  </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bankInput">Bank</label>
                                    <select class="form-control" style="height: 42px;border: 2px solid black;" id="bankInput" name="bankInput" required>
                                        <option value="" disabled selected>Select bank</option>
                                        @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{$bankAccount->id}}">{{App\Models\Bank::where('id', $bankAccount->bankID)->first()->name}} {{$bankAccount->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valueInput">Value</label>
                                    <input type="number" step="0.01" class="form-control" id="valueInput" name="valueInput" placeholder="Value" required
                                    style="min-width: 100px;border: 2px solid black;">                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valueInput">Comment</label>
                                    <input type="text"  class="form-control" id="commentInput" name="commentInput" placeholder="Comment" required style="min-width: 100px;border: 2px solid black;" >
                                </div>
                            </div>
                        </div>
                        <input type="submit" name="submit" class="btn btn-dark btn-md" value="Submit">
                        <input type="hidden" name="_token" value="{{Session::token()}}">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Temp Transactions</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Temp Transactions</h3>
                    <form action="{{route('settlePosTransactions')}}" method="post">
                        <table  id="transactionsTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table " style="width:100%">
                            <thead style="width:100%">
                                <tr>
                                    <th scope="col" style="text-align:center">Date</th>
                                    <th scope="col" style="text-align:center">Account</th>
                                    <th scope="col" style="text-align:center">Value</th>
                                    <th scope="col" style="text-align:center">Comment</th>
                                    <th scope="col" style="text-align:center">Confirm</th>
                                    <th scope="col" style="text-align:center">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $trans)
                                    <tr>
                                        <td style="text-align:center">{{$trans->date}}</td>
                                        <td style="text-align:center">
                                            {{App\Models\Bank::where('id',App\Models\Account::where('id',$trans->accountId)->first()->bankID)->first()->name}} {{App\Models\Account::where('id',$trans->accountId)->first()->name}}
                                        </td>
                                        <td style="text-align:center">{{number_format($trans->value)}}</td>
                                        <td style="text-align:center">{{$trans->comment}}</td>
                                        <td style="text-align:center">
                                            <a class="btn btn-success " style="height:25px;padding: 3px 8px;padding-bottom: 3px;" onclick="confirmSettling({{$trans->id}})"  role="button">Confirm</a>
                                        </td>
                                        <td style="text-align:center">
                                            <a class="btn btn-danger delete-confirm" style="height:25px;padding: 3px 8px;padding-bottom: 3px;"  href="{{route('deleteTempTransaction',[$trans->id])}}" role="button">Delete</a>
                                        </td>
                                    </tr>  
                                @endforeach   
                            </tbody>
                        </table>
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
            <h5 class="modal-title" id="exampleModalLabel">Settle Transaction</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="{{ url('/') }}" method="post" id="modalForm">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descriptionInput">Description</label>
                            <input type="text"  class="form-control" id="descriptionInput" name="descriptionInput" placeholder="Description" required style="min-width: 100px;border: 2px solid black;" >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="clientInput">Client Name</label>
                            <input type="text"  class="form-control" id="clientInput" name="clientInput" placeholder="Client Name" required style="min-width: 100px;border: 2px solid black;" >
                        </div>
                    </div>
                </div>
                @if(Auth::user()->admin)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="brandInput">Brand</label>
                                <select class="form-control" style="min-width: 100px;border: 2px solid black;" id="brandInput" name="brandInput" required >
                                    <option value="" disabled selected>Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endif
                <input type="submit" name="submit" class="btn btn-dark btn-md" value="Confirm">
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
        var path = $("#modalForm").attr('action')+ "/confirmTempTransaction/" +transId;
        $("#modalForm").attr('action', path );
    };
</script>
@endsection