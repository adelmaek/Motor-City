@extends('layouts.app')



@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Transaction</div>
                <div class="card-body" id="inputs">
                    @if(!strcmp('cash', $accountType))
                        <h3 class="card-title">Query Cash Transactions</h3>
                    @elseif(!strcmp('cashDollar', $accountType))
                        <h3 class="card-title">Query Cash Dollar Transactions</h3>
                    @elseif(!strcmp('custodyCash', $accountType))
                        <h3 class="card-title">Query Custody Cash Transactions</h3>
                    @elseif(!strcmp('check', $accountType))
                        <h3 class="card-title">Query Checks Transactions</h3>
                    @elseif(!strcmp('visa', $accountType))
                        <h3 class="card-title">Query Visa Transactions</h3>
                    @endif
                    <form action="{{route('queryBrandAccountTransaction',[$accountType])}}" method="post">
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
                                    <input type="date" class="form-control" id="toDateInput" name="toDateInput" style="height: 42px;" value={{$todayDate}} required>     
                                  </div>
                            </div>
                            @if (Auth::user()->admin)
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
                            @endif
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
                                @if(!strcmp("check",$accountType))
                                    <th scope="col" style="text-align:center">Validity Date</th>
                                @endif
                                <th scope="col" style="text-align:center">Deposition</th>
                                <th scope="col" style="text-align:center">Withdrawal</th>
                                <th scope="col" style="text-align:center">Current Balance</th>
                                <th scope="col" style="text-align:center">Description</th>
                                <th scope="col" style="text-align:center">Client</th>
                                @if(!strcmp("check",$accountType))
                                    <th scope="col" style="text-align:center">Settle</th>
                                @endif
                                <th scope="col" style="text-align:center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($transactions as $trans)
                            <tr>
                                <td style="text-align:center">{{$trans->date}}</td>
                                @if(!strcmp("check",$accountType))
                                    <td style="text-align:center">{{$trans->checkValidityDate}}</td>
                                @endif
                                @if(!strcmp($trans->type,"add"))
                                    <td style="text-align:center">{{$trans->value}}</td>
                                @else
                                    <td style="text-align:center"> - </td>
                                @endif
                                @if(!strcmp($trans->type,"sub"))
                                    <td style="text-align:center">{{$trans->value}}</td>
                                @else
                                    <td style="text-align:center"> - </td>
                                @endif
                                <td style="text-align:center">{{$trans->currentBalance}}</td>
                                <td style="text-align:center">{{$trans->description}}</td>
                                <td style="text-align:center">{{$trans->clientName}}</td>
                                @if(!strcmp("check",$accountType))
                                    <td style="text-align:center">
                                        @if(!$trans->settled)
                                            @if(\Carbon\Carbon::parse($trans->checkValidityDate)->gt(\Carbon\Carbon::parse($todayDate)))
                                                <a class="btn btn-info disabled"  style="height:25px;padding: 3px 8px;padding-bottom: 3px;" role="button">Settle</a>
                                            @else
                                                <a class="btn btn-info " data-toggle="modal" data-target="#settlingModal" style="height:25px;padding: 3px 8px;padding-bottom: 3px;"  role="button">Settle</a>
                                            @endif
                                        @endif
                                        @if($trans->settled)
                                            @if(!$trans->confirmSettling)
                                                @if(\Carbon\Carbon::parse($trans->checkSettlingDate)->gt(\Carbon\Carbon::parse($todayDate)))
                                                    <a class="btn btn-info disabled"  style="height:25px;padding: 3px 8px;padding-bottom: 3px;" role="button">Confirm Settling</a>
                                                @else
                                                    <a class="btn btn-info " style="height:25px;padding: 3px 8px;padding-bottom: 3px;" href="{{route('confirmSettling',[$trans->id])}}" role="button" >Confirm Settling</a>
                                                @endif
                                            @else
                                                Settled to {{App\Models\Bank::where('id',App\Models\Account::where('id',$trans->checKToBankId)->first()->bankID)->first()->name}} {{App\Models\Account::where('id',$trans->checKToBankId)->first()->name}}
                                            @endif

                                        @endif
                                    </td>
                                @endif
                                <td style="text-align:center">
                                    <a class="btn btn-danger delete-confirm " style="height:25px;padding: 3px 8px;padding-bottom: 3px;" href="{{route('deleteTransaction',[$trans->id])}}" role="button" >Delete</a>
                                </td>
                            </tr>
                            {{-- Modal for each transaction to use transaction id --}}
                            <div class="modal fade" id="settlingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered " role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('settleCheck',[$trans->id])}}" method="post">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="settlingDateInput">To Date</label>
                                                        <input type="date" class="form-control" id="settlingDateInput" name="settlingDateInput" style="height: 42px;" value={{$todayDate}} required>     
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="settlingBankInput">Settling Bank</label>
                                                        <select class="form-control" style="height: 42px;" id="settlingBankInput" name="settlingBankInput" >
                                                            <option value="" disabled selected>Settle to bank account</option>
                                                            @foreach ($bankAccounts as $bankAccount)
                                                                <option value="{{$bankAccount->id}}">{{App\Models\Bank::where('id',$bankAccount->bankID)->first()->name}} {{$bankAccount->name}}</option>
                                                            @endforeach
                                                            <option value="-1">Others</option>
                                                        </select>
                                                      </div>
                                                </div>
                                            </div>
                                            <input type="submit" name="submit" class="btn btn-dark btn-md" value="Settle">
                                            <input type="hidden" name="_token" value="{{Session::token()}}">
                                        </form>
                                    </div>
                                  </div>
                                </div>
                            </div>
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