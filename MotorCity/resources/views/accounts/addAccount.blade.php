@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Accounts</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Create Account</h3>
                    <form action="{{route('addAccount')}}" method="post">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="accountType">Account Type</label>
                                    <select class="form-control accountType" style="height: 42px;" id="accountType" name="accountType" required>
                                        <option value="" disabled selected>Select account</option>
                                        <option value="bank" >Bank Account</option>
                                        <option value="visa">POS</option>
                                        {{-- <option value="others" disabled>Others</option> --}}
                                    </select>
                                  </div>
                            </div>
                            <div class="col-md-4" id="bankNameDiv" name="bankNameDiv">
                                <div class="form-group" >
                                    <label for="bankNameInput" >Bank Name</label>
                                    <input list="bankNameInputList" class="form-control" id="bankNameInput" name="bankNameInput" placeholder="Bank name" >
                                    <datalist id="bankNameInputList">
                                        @foreach ($banks as $bank)
                                            <option value="{{$bank->name}}">{{$bank->name}}</option>
                                        @endforeach
                                    </datalist>
                                        
                                    {{-- </datalist> --}}
                                </div>
                            </div>
                            <div class="col-md-4" id="posDiv" name="posDiv">
                                <div class="form-group" >
                                    <label for="posBankAccountInput" >POS bank account</label>
                                    <select class="form-control accountType" style="height: 42px;"id="posBankAccountInput" name="posBankAccountInput" placeholder="POS bank account">
                                        <option value="" disabled selected>POS bank account</option>
                                        @foreach ($bankAccounts as $bankAccount)
                                            <option value="{{$bankAccount->id}}">{{App\Models\Bank::where('id', $bankAccount->bankID)->first()->name}} {{$bankAccount->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" >
                                    <label for="nameInput">Account Name</label>
                                    <input type="text" class="form-control" id="nameInput" name="nameInput" placeholder="Balance Name or Number"  required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="valueInput">Initial Value</label>
                                    <input type="number" step ="0.01" class="form-control" id="valueInput" name="valueInput" placeholder="Value" required style="min-width: 100px;" >
                                  </div>
                            </div>
                            <div class="col-md-4" id="brandDiv" name="brandDiv">
                                <div class="form-group">
                                    <label for="brandInput">Brand</label>
                                    <select class="form-control" style="height: 42px;" id="brandInput" name="brandInput" >
                                        <option value="" disabled selected>Select Brand</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{$brand->id}}" >{{$brand->name}}</option>    
                                        @endforeach             
                                    </select>
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
        <div class="col-md-4">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Brands</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Add A Brand</h3>
                    <br>
                    <br>
                    <form action="{{route('addBrand')}}" method="post">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nameInput">Brand Name</label>
                                    <br>
                                    <br>
                                    <input type="text" class="form-control" id="nameInput" name="nameInput" placeholder="Brand name"  required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <input type="submit" name="submit" class="btn btn-dark btn-md" value="Submit">
                        <input type="hidden" name="_token" value="{{Session::token()}}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@section('extraJS')
<script>
    $(document).ready(function(){
        $("select.accountType").change(function(){
            var selectedAccountType = $(this).children("option:selected").val();
            if(!selectedAccountType.localeCompare("bank"))
            {
                $('#bankNameDiv').show();
                $("#bankNameInput").prop('required',true);
                $('#posDiv').hide();
                $('#brandDiv').hide();
                $("#posBankAccountInput").prop('required',false);
                $('#brandInput').prop('required',false);
            }
            else if (!selectedAccountType.localeCompare("visa"))
            {
                $('#bankNameDiv').hide();
                $('#posDiv').show();
                $("#posBankAccountInput").prop('required',true);
                $('#brandDiv').show();
                $('#brandInput').prop('required',true);
                $("#bankNameInput").prop('required',false);
            }               
        });
    });
</script>
@endsection