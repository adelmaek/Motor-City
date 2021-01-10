@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card border-dark">
                <div class="card-header m-b-0 text-white bg-dark">Transaction</div>
                <div class="card-body" id="inputs">
                    <h3 class="card-title">Query Transactions</h3>
                    <form action="{{route('queryBrandAllTransactions')}}" method="post">
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
                                    <input type="date" class="form-control" id="toDateInput" name="toDateInput" style="height: 42px;" required>     
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
                                <th scope="col" style="text-align:center"><div>Date</div> <div><br></div></th>
                                <th scope="col" style="text-align:center"><div>Cash</div> <div>{{number_format($cashBalance)}}</div></th>
                                <th scope="col" style="text-align:center"><div>Custody Cash</div> <div>{{number_format($custodyCashBalance)}}</div></th>
                                <th scope="col" style="text-align:center"><div>Cash Dollar</div> <div>{{number_format($cashDollarBalance)}}</div></th>
                                <th scope="col" style="text-align:center"><div>Checks</div> <div>{{number_format($checKBalance)}}</div></th>
                                <th scope="col" style="text-align:center"><div>Visa</div> <div>{{number_format($visaBalance)}}</div></th>
                                <th scope="col" style="text-align:center"><div>Banks</div> <div>{{number_format($banksBalance)}}</div></th>
                                <th scope="col" style="text-align:center"><div>Description</div> <div><br></div></th>
                                <th scope="col" style="text-align:center"><div>Client Name</div> <div><br></div></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($transactionsRows as $row)
                            <tr>
                                <td style="text-align:center; border: 1px solid black;">{{$row->date}}</td>
                                <td style="text-align:center; border: 1px solid black;">{{number_format($row->cash)}}</td>
                                <td style="text-align:center; border: 1px solid black;">{{number_format($row->custodyCash)}}</td>
                                <td style="text-align:center; border: 1px solid black;">{{number_format($row->cashDollar)}}</td>
                                <td style="text-align:center; border: 1px solid black;">{{number_format($row->check)}}</td>
                                <td style="text-align:center; border: 1px solid black;">{{number_format($row->visa)}}</td>
                                <td style="text-align:center; border: 1px solid black;">{{number_format($row->banks)}}</td>
                                <td style="text-align:center; border: 1px solid black;">{{$row->description}}</td>
                                <td style="text-align:center; border: 1px solid black;">{{$row->clientName}}</td>
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
        "scrollY":"1000px",
        "sScrollX": "100%",
        responsive: true,
        "scrollCollapse": true,
        "paging":         false
    });
    $(' .buttons-print,.buttons-excel').addClass('btn btn-primary mr-1');
</script>
@endsection