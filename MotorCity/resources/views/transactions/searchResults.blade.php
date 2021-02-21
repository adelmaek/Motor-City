@extends('layouts.app')



@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Transactions</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Search Results</h3>
                    <form action="{{route('settlePosTransactions')}}" method="post">
                        <table  id="transactionsTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table " style="width:100%">
                            <thead style="width:100%">
                                <tr>
                                    <th scope="col" style="text-align:center">Date</th>
                                    <th scope="col" style="text-align:center">Deposit</th>
                                    <th scope="col" style="text-align:center">Withdrawal</th>
                                    <th scope="col" style="text-align:center">Current Balance</th>
                                    <th scope="col" style="text-align:center">Account</th>
                                    <th scope="col" style="text-align:center">Brand</th>
                                    <th scope="col" style="text-align:center">Description</th>
                                    <th scope="col" style="text-align:center">Client</th>
                                    {{-- <th scope="col" style="text-align:center">Delete</th> --}}
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
                                        <td style="text-align:center">
                                            @if(!strcmp(App\Models\Account::where('id',$trans->accountId)->first()->type, "bank"))
                                                {{App\Models\Bank::where('id',App\Models\Account::where('id',$trans->accountId)->first()->bankID)->first()->name}} {{App\Models\Account::where('id',$trans->accountId)->first()->name}}
                                            @elseif(!strcmp(App\Models\Account::where('id',$trans->accountId)->first()->type, "visa"))
                                                {{App\Models\Account::where('id',$trans->accountId)->first()->name}}
                                            @elseif(!strcmp(App\Models\Account::where('id',$trans->accountId)->first()->type, "cashDollar"))
                                                $
                                            @else
                                                {{App\Models\Account::where('id',$trans->accountId)->first()->type}}
                                            @endif
                                        </td>
                                        <td style="text-align:center">{{App\Models\Brand::where('id', $trans->brandId)->first()->name}}</td>
                                        <td style="text-align:center">{{$trans->description}}</td>
                                        <td style="text-align:center">{{$trans->clientName}}</td>
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
@endsection