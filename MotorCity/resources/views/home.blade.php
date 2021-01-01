@extends('layouts.app')

@section('content')
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
                                    <select class="form-control" style="height: 42px;" id="brandIdInput" name="brandIdInput" required>
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
                                    <select class="form-control balanceInput" style="height: 42px;" id="balanceInput" name="balanceInput" required>
                                        <option value="" disabled selected>Select Balance</option>
                                        <option value="cash">Cash</option>
                                        <option value="cashDollar">Cash $</option>
                                        <option value="custodyCash">Custody cash</option>
                                        <option value="check">Check</option>
                                        <option value="visa">Visa</option>
                                        @foreach ($bankAccounts as $bankAccount)
                                            <option value="{{$bankAccount->id}}">{{$bankAccount->name}}</option>
                                        @endforeach
                                    </select>
                                  </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="typeInput">Type</label>
                                    <select class="form-control" style="height: 42px;" id="typeInput" name="typeInput" required>
                                        <option value="" disabled selected>Add/Withdraw</option>
                                        <option value="add">Add</option>
                                        <option value="sub">Withdraw</option>
                                    </select>
                                  </div>
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valueInput">Value</label>
                                    <input type="number" step ="0.01" class="form-control" id="valueInput" name="valueInput" placeholder="Value" required style="min-width: 100px;" >
                                  </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dateInput">Date</label>
                                    <input type="date" class="form-control" id="dateInput" name="dateInput" style="height: 42px;" required>     
                                  </div>
                            </div>
                        </div>
                        <div class="row" id="checkBanks">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="checkIsFromBankInput">Check form bank</label>
                                    <select class="form-control" style="height: 42px;" id="checkIsFromBankInput" name="checkIsFromBankInput" >
                                        <option value="" disabled selected>From bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="bank->id">{{$bank->name}}</option>
                                        @endforeach
                                        <option value="others">Others</option>
                                    </select>
                                  </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="checkIsToBankInput">Check to bank</label>
                                    <select class="form-control" style="height: 42px;" id="checkIsToBankInput" name="checkIsFromBankInput" >
                                        <option value="" disabled selected>To bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="bank->id">{{$bank->name}}</option>
                                        @endforeach
                                    </select>
                                  </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="noteInput">Description</label>
                                    <textarea class="form-control" id="noteInput" name="noteInput" rows="2" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="clientNameInput">Client Name</label>
                                    <input type="text" class="form-control" id="clientNameInput" name="clientNameInput" rows="2" required>
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
                <div class="card-body" id="Accounts" >
                    <h3 class="card-title">Accounts</h3>
                    <div class="table-responsive-sm">
                        <table  id="accountsTable" class="table color-bordered-table table-striped full-color-table full-info-table hover-table ">
                            <thead>
                                <tr>
                                <th scope="col" style="text-align:center">Account Name</th>
                                <th scope="col" style="text-align:center">Current Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($currentUserAccounts as $account)
                                <tr>
                                    <td style="text-align:center">{{$account->name}}</td>
                                    <td style="text-align:center">{{$account->balance}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@section('extraJS')
<script>
    $(document).ready(function(){
        $("select.balanceInput").change(function(){
            var selectedBalance = $(this).children("option:selected").val();
            if(!selectedBalance.localeCompare("check"))
                $('#checkBanks').show();
            else
                $('#checkBanks').hide();
        });
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
            "paging":         false

        });
        $(' .buttons-print,.buttons-excel').addClass('btn btn-primary mr-1');
    </script>
@endsection