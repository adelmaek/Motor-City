@extends('layouts.app')

@section('content')
{{-- <div class="alert alert-danger" role="alert" id="formValidationAlert">
</div> --}}
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Transaction</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Insert Transaction</h3>
                    <form action="{{route('addTransaction')}}" method="post">
                        @if (Auth::user()->admin)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="brandIdInput">Brand</label>
                                    <select class="form-control" style="height: 42px;border: 2px solid black;" id="brandIdInput" name="brandIdInput" required>
                                        <option value="" disabled selected>Choose brand</option>
                                        @foreach ($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="balanceInput">Balance Name</label>
                                    <select class="form-control balanceInput" style="height: 42px;border: 2px solid black;" id="balanceInput" name="balanceInput" required>
                                        <option value="" disabled selected>Select Balance</option>
                                        <option value="cash">Cash EGP</option>
                                        <option value="cashDollar">Cash USD ($)</option>
                                        {{-- <option value="custodyCash">Custody cash</option> --}}
                                        <option value="check">شيكات</option>
                                        <option value="banks">تحويل بنك</option>
                                        <option value="banks">ايداع بنك</option>
                                        <option value="pos">Credit Card - بطاقه ائتمان</option>
                                        {{-- <option value="visa">POS</option> --}}
                                        {{-- <option value="bankToBank">Bank to bank</option> --}}
                                        {{-- @foreach ($bankAccounts as $bankAccount)
                                            <option value="{{$bankAccount->id}}">{{App\Models\Bank::where('id', $bankAccount->bankID)->first()->name}} {{$bankAccount->name}}</option>
                                        @endforeach
                                        @foreach ($posAccounts as $posAccount)
                                        <option value="{{$posAccount->id}}">{{$posAccount->name}}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="typeInput">Type</label>
                                    <select class="form-control" style="height: 42px;border: 2px solid black;" id="typeInput" name="typeInput" required>
                                        {{-- <option value="" disabled selected>Add/Withdraw</option> --}}
                                        <option value="add" selected>ايداع</option>
                                        <option value="sub">سحب</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="banksList">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bankAccountId">Bank account</label>
                                    <select class="form-control" style="height: 42px;border: 2px solid black;" id="bankAccountId" name="bankAccountId">
                                        @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{$bankAccount->id}}">{{App\Models\Bank::where('id', $bankAccount->bankID)->first()->name}} {{$bankAccount->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="posList">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="posAccountId">POS account</label>
                                    <select class="form-control" style="height: 42px;border: 2px solid black;" id="posAccountId" name="posAccountId">
                                        @foreach ($posAccounts as $posAccount)
                                        <option value="{{$posAccount->id}}">{{$posAccount->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="cashWithdrawalReasonDiv">
                            <div class="col-md-6">
                                <label for="cashWithdrawalReason">Withdrawal reason</label>
                                <input list="cashWithdrawalReasonList" class="form-control" style="height: 42px;border: 2px solid black;" id="cashWithdrawalReason" name="cashWithdrawalReason"
                                    placeholder="Reason">
                                <datalist id="cashWithdrawalReasonList">
                                    <option value="bank">bank</option>
                                    <option value="refund">refund</option>
                                </datalist>
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
                                    <label for="dateInput">Date</label>
                                    <input type="date" class="form-control" id="dateInput" name="dateInput" style="height: 42px;border: 2px solid black;" value="{{$today}}" max="{{$today}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="checkSpecialInput">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="checkIsFromBankInput">اسم البنك</label>
                                    <input list="checkIsFromBankInputList" class="form-control" style="height: 42px;border: 2px solid black;" id="checkIsFromBankInput" name="checkIsFromBankInput"
                                        placeholder="Bank name">
                                    <datalist id="checkIsFromBankInputList">
                                        @foreach ($banks as $bank)
                                        <option value="{{$bank->name}}">{{$bank->name}}</option>
                                        @endforeach
                                    </datalist>
                                    {{-- <select class="form-control" style="height: 42px;border: 2px solid black;" id="checkIsFromBankInput" name="checkIsFromBankInput" >
                                        <option value="" disabled selected>From bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{$bank->name}}">{{$bank->name}}</option>
                                    @endforeach
                                    <option value="-1">Others</option>
                                    </select> --}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="checkNumberInput">رقم الشيك</label>
                                    <input type="text" class="form-control" id="checkNumberInput" name="checkNumberInput" placeholder="Check Number" style="min-width: 100px;border: 2px solid black;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="checkValidityDateInput">تاريخ الشيك</label>
                                    <input type="date" class="form-control" id="checkValidityDateInput" value="{{$today}}" name="checkValidityDateInput" style="height: 42px;border: 2px solid black;">
                                </div>
                            </div>
                        </div>
                        <div class="row" id="bankToBankSpecialInput">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fromBank">Form bank</label>
                                    <select class="form-control" style="height: 42px;border: 2px solid black;" id="fromBank" name="fromBank">
                                        <option value="" disabled selected>From bank</option>
                                        @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{$bankAccount->id}}">{{App\Models\Bank::where('id', $bankAccount->bankID)->first()->name}} {{$bankAccount->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="toBank">To bank</label>
                                    <select class="form-control" style="height: 42px;border: 2px solid black;" id="toBank" name="toBank">
                                        <option value="" disabled selected>To bank</option>
                                        @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{$bankAccount->id}}">{{App\Models\Bank::where('id', $bankAccount->bankID)->first()->name}} {{$bankAccount->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="noteInput">Description</label>
                                    <textarea class="form-control" id="noteInput" name="noteInput" rows="2" style="border: 2px solid black;" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="clientNameInput">Client Name</label>
                                    <input type="text" class="form-control" id="clientNameInput" name="clientNameInput" style="border: 2px solid black;" rows="2" required>
                                </div>
                            </div>
                        </div>
                        {{-- <button type="submit" class="btn btn-dark">Submit</button> --}}
                        <input type="submit" name="submit" class="btn btn-dark btn-md" value="Submit">
                        <input type="hidden" name="_token" value="{{Session::token()}}">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Accounts</div>
                <div class="card-body" id="Accounts">
                    <h3 class="card-title">Accounts</h3>
                    <div class="table-responsive-sm">
                        <table id="accountsTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table ">
                            <thead>
                                <tr>
                                    <th scope="col" style="text-align:center">Account Name</th>
                                    <th scope="col" style="text-align:center">Current Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($currentUserAccounts as $account)
                                <tr>
                                    <td style="text-align:center">{{$account->accountName}}</td>
                                    <td style="text-align:center">{{number_format($account->balance)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Latest Transaction</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Latest User Transactions</h3>
                    <table id="transactionsTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table " style="width:100%">
                        <thead style="width:100%">
                            <tr>
                                <th scope="col" style="text-align:center">Date</th>
                                <th scope="col" style="text-align:center">Type</th>
                                <th scope="col" style="text-align:center">Brand</th>
                                <th scope="col" style="text-align:center">Account</th>
                                <th scope="col" style="text-align:center">Value</th>
                                <th scope="col" style="text-align:center">Description</th>
                                <th scope="col" style="text-align:center">Client</th>
                                <th scope="col" style="text-align:center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $trans)
                            <tr>
                                <td style="text-align:center">{{$trans->date}}</td>
                                @if(!strcmp('add', $trans->type))
                                <td style="text-align:center">Deposit</td>
                                @else
                                <td style="text-align:center" class='table-danger'>Withdrawal</td>
                                @endif
                                <td style="text-align:center">{{App\Models\Brand::where('id', $trans->brandId)->first()->name}}</td>
                                <td style="text-align:center">{{$trans->accountName}}</td>
                                <td style="text-align:center;" class='{{($trans->value<0) ? 'table-danger' : '' }}'>{{number_format($trans->value)}}</td>
                                <td style="text-align:center" onclick="editDescription({{$trans->id}});">{{$trans->description}}</td>
                                <td style="text-align:center" onclick="editClientName({{$trans->id}});">{{$trans->clientName}}</td>
                                <td style="text-align:center">
                                    <a class="btn btn-danger delete-confirm" style="height:25px;padding: 3px 8px;padding-bottom: 3px;" href="{{route('deleteTransaction',[$trans->id])}}"
                                        role="button">Delete</a>
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
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit here</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/') }}" method="post" id="modalForm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editInput">Edit here</label>
                                <textarea class="form-control" id="editInput" name="editInput" rows="2" style="border: 2px solid black;" required></textarea>
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="submit" class="btn btn-dark btn-md" value="Edit">
                    <input type="hidden" name="_token" value="{{Session::token()}}">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection



@section('extraJS')
<script>
    $(document).ready(function(){

        // $("#formValidationAlert").hide()
        // $("select.balanceInput").change(function(){
        //     $("#formValidationAlert").hide()
        // });
        $("#banksList").hide()
        $("#posList").hide()
        $("#cashWithdrawalReasonDiv").hide()
        $("select.balanceInput").change(function(){
            var selectedBalance = $(this).children("option:selected").val();
            if(!selectedBalance.localeCompare("check"))
                $('#checkSpecialInput').show();
            else
                $('#checkSpecialInput').hide();

            if(!selectedBalance.localeCompare("bankToBank"))
                $('#bankToBankSpecialInput').show();
            else
                $('#bankToBankSpecialInput').hide();

            if(!selectedBalance.localeCompare("banks"))
            {
                $("#banksList").show()
                $('#banksList').prop('required',true);
            }
            else
            {
                $("#banksList").hide()
                $('#banksList').prop('required',false);
            }
            if(!selectedBalance.localeCompare("pos"))
            {
                $("#posList").show()
                $('#posList').prop('required',true);
            }
            else
            {
                $("#posList").hide()
                $('#posList').prop('required',false);
            }
            if(!selectedBalance.localeCompare("cash"))
            {
                if(!($("#typeInput").val()).localeCompare('sub'))
                {
                    $("#cashWithdrawalReasonDiv").show()
                }
                else
                {
                    $("#cashWithdrawalReasonDiv").hide()   
                }
            }
            else
            {
                $("#cashWithdrawalReasonDiv").hide()   
            }
        });
        $("#typeInput").change(function(){
            
            if(!($("#typeInput").val()).localeCompare('sub'))
            {
                if(!($("#balanceInput").val()).localeCompare('cash'))
                    $("#cashWithdrawalReasonDiv").show()
                else
                    $("#cashWithdrawalReasonDiv").hide()
            }
            else
            {
                $("#cashWithdrawalReasonDiv").hide()   
            }
        });
        // $("#toBank").change(function(){
        //     var toBank = $("#toBank").val();
        //     var fromBank = $( "#fromBank" ).val();
        //     if(fromBank === toBank)
        //     {
        //         $("#formValidationAlert").html("Invalid transaction from bank to same bank.")
        //         $("#formValidationAlert").show()
        //     }
        //     else
        //         $("#formValidationAlert").hide()
                
        // });

        // $("#fromBank").change(function(){
        //     var toBank = $("#toBank").val();
        //     var fromBank = $( "#fromBank" ).val();
        //     if(fromBank === toBank)
        //     {
        //         $("#formValidationAlert").html("Invalid transaction from bank to same bank.")
        //         $("#formValidationAlert").show()
        //     }
        //     else
        //         $("#formValidationAlert").hide()
                
        // });
       
    });
</script>
<script>
    $('#accountsTable').DataTable({
            "displayLength": 5,
            "processing": true,
            dom: 'Bfrtip',
            buttons: [
                    {
                    extend: 'excel',
                    title: 'Motor-City-Accounts',
                    footer: true
                }
            ]   ,
            "scrollY":        "390px",
            "scrollCollapse": true,
            "paging":         false,
            "order": []

        });
        $(' .buttons-print,.buttons-excel').addClass('btn btn-primary mr-1');
</script>
<script>
    $('#transactionsTable').DataTable({
        "displayLength": 5,
        "processing": true,
        dom: 'Bfrtip',
        buttons: [
                {
                extend: 'excel',
                title: 'Motor-City-Transactions',
                footer: true,
                exportOptions: {
                columns: ':not(:last-child)',
                }
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
    function editDescription(transId){
        $('#editModal').modal('toggle');
        var path = $("#modalForm").attr('action')+ "/editDescription/" +transId;
        $("#modalForm").attr('action', path );
    };
    function editClientName(transId){
        $('#editModal').modal('toggle');
        var path = $("#modalForm").attr('action')+ "/editClientName/" +transId;
        $("#modalForm").attr('action', path );
    };
</script>
@endsection