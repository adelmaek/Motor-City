@extends('layouts.app')



@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Transaction</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Query Bank Account Transactions</h3>
                    <form action="{{route('queryBankAccountTransaction',[$accountId])}}" method="post">
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
                    <table  id="transactionsTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table " style="width:100%">
                        <thead style="width:100%">
                            <tr>
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
                                @if(\Carbon\Carbon::parse($trans->date)->gte(\Carbon\Carbon::parse($yesterday)))
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
                </div>
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
        "paging":         false
    });
    $(' .buttons-print,.buttons-excel').addClass('btn btn-primary mr-1');
</script>
@endsection