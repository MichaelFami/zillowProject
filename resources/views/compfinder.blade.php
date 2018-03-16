@extends('master') @section('styles')
<link rel="stylesheet" href="css/style.css"> @endsection @section('content')

<!-- Modal -->
<div class="modal fade" id="modalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Monthly Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
    </div>
</div>

<div class="overlay">

</div>
<div class="jumbotron jumbotron-fluid" id="jumboHouse">
    <div class="container">
        <h1 id="titleJumbo" class="display-4">Compfinder</h1>
        <div class="row sepRows">
            <h5 id="addressHeading">Address</h5>
            <h5 class="searchHeading">Zipcode</h5>
            <h5 class="searchHeading">Number of Comps(1-25)</h5>
        </div>
        <div class="row sepRows">
            <div class="col">
                <form class="" action="get_CompData" method="post">
                    @csrf
                    <input type="text" name="addressListing" placeholder="Use Spaces" value="">
                    <input class="firstInputs" type="text" name="zipListing" placeholder="Zipcode" value="">
                    <input class="firstInputs" type="text" name="numberComps" placeholder="Number of Comps" value="">
                    <button class="button btn" type="submit" name="button">Search</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">



    <div class="row">
        <div class="col-8">

            <div class="row sepRows">
                <div class="col">

                    @if(!empty($compData))
                    <h3>Principal Property</h3>
                    <table class="table">

                        <thead>
                            <th>Address</th>
                            <th>City</th>
                            <th>Value</th>
                            <th>Square Feet</th>
                            <th>Price Per SF</th>
                            <th>Listing</th>
                        </thead>
                        <tbody>

                            <tr>
                                <td>{{$compData['response']['properties']['principal']['address']['street']}}</td>
                                <td>{{$compData['response']['properties']['principal']['address']['city']}}</td>
                                @if( !is_array($compData['response']['properties']['principal']['zestimate']['amount']) )
                                <td>${{$compData['response']['properties']['principal']['zestimate']['amount']}}</td>
                                @else
                                <td>
                                    No Estimate Available
                                </td>
                                @endif @if( !is_array($compData['response']['properties']['principal']['finishedSqFt']) )
                                <td>{{$compData['response']['properties']['principal']['finishedSqFt']}}</td>
                                @else
                                <td>
                                    No Square Feet Available
                                </td>
                                @endif

                                <td>{{money_format('%.2n',($compData['response']['properties']['principal']['zestimate']['amount']/$compData['response']['properties']['principal']['finishedSqFt']))}}</td>
                                <td><a href="{{$compData['response']['properties']['principal']['links']['homedetails']}}" target="_blank">
                                <button class="btn button"type="button"  name="button" >Listing</button></a></td>

                            </tr>

                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col">
                    @if(!empty($compData))
                    <h3>Comparable Properties</h3>
                    <table class="table">
                        <thead>
                            <th>Address</th>
                            <th>City</th>
                            <th>Value</th>
                            <th>Square Feet</th>
                            <th>Price Per SF</th>
                            <th>Listing</th>
                        </thead>
                        <tbody>

                            @foreach($compData['response']['properties']['comparables']['comp'] as $comp)
                            <tr>
                                <td>{{$comp['address']['street']}}</td>
                                <td>{{$comp['address']['city']}}</td>
                                @if( !is_array($comp['zestimate']['amount']) )
                                <td>${{$comp['zestimate']['amount']}}</td>
                                @else
                                <td>
                                    No Estimate Available
                                </td>
                                @endif @if( !empty($comp['finishedSqFt']) )
                                <td>{{$comp['finishedSqFt']}}</td>
                                <td>{{money_format('%.2n',$comp['zestimate']['amount']/$comp['finishedSqFt'])}}</td>


                                @else
                                <td>
                                    No Estimate Available
                                </td>
                                <td>
                                    No Estimate Available
                                </td>
                                @endif


                                <td><a href="{{$comp['links']['homedetails']}}" target="_blank">
                                <button class="btn button"type="button"  name="button" >Listing</button></a></td>

                            </tr>
                            @endforeach


                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
        <div class="col">
                @if(!empty($compData))
                    <h5 >Average Price Per Square Foot</h5>
                    <div class="row">

                        <div class="col">
                            <?php $sum= 0;  ?>
                            @foreach($compData['response']['properties']['comparables']['comp'] as $comp)
                                @if( !empty($comp['finishedSqFt']) )
                                    <?php $ppsf = ($comp['zestimate']['amount']/$comp['finishedSqFt']);
                                        $sum += $ppsf; ?>
                                @else
                                    <?php $sum += 0;?>
                                @endif
                            @endforeach
                            <?php $avppsf = money_format('%.2n', ($sum/count($compData['response']['properties']['comparables']['comp'])));
                            echo $avppsf; ?>
                        </div>

                    </div>
                    <h5>Investment Recommendation</h5>
                    <div class="row">
                        <div class="col">
                            <?php if (($compData['response']['properties']['principal']['zestimate']['amount']/$compData['response']['properties']['principal']['finishedSqFt']) < $avppsf) {
                                echo "Buy";
                            } else {
                                echo "Do Not Buy";
                            }?>
                        </div>
                    </div>


            <div class="row form-group" id="calculator">
                <div class="col">


                    <form action="getLoanCalculation" method="post">
                        <h5>Loan Payment Calculator</h5>
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label" for="housePrice">House Price:</label>
                            <div class="input-group col-sm-6">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">$</div>
                                </div>
                                <input name="housePrice" type="text" class="form-control" id="housePrice" value={{$compData['response']['properties']['principal']['zestimate']['amount']}}>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label" for="loanTerm">Loan term:</label>
                            <div class="input-group col-sm-6">
                                <input name="loanTerm" type="text" class="form-control" id="loanTerm" aria-describedby="basicAddOn2" value="15">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basicAddOn2">years</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label" for="interestRate">Interest Rate:</label>
                            <div class="input-group col-sm-6">
                                <input name="interestRate" type="text" class="form-control" id="interestRate" aria-describedby="basicAddOn3" value="5.5">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basicAddOn3">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label" for="downPayment">Down Payment:</label>
                            <div class="input-group col-sm-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basicAddOn4">$</span>
                                </div>
                                <input name="downPayment" type="text" class="form-control" id="downPayment" aria-describedby="basicAddOn4" value="10000">

                            </div>
                        </div>

                        <button id="calculate" class="btn" data-toggle="modal" data-target="#exampleModalCenter" type="button" name="button">Calculate</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection @section('javascript')
<script>
    /* must apply only after HTML has loaded */
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#calculate").on('click', function() {
            var housePrice = $("#housePrice").val();
            var loanTerm = $("#loanTerm").val();
            var interestRate = $("#interestRate").val();
            var downPayment = $("#downPayment").val();

            postData = {
                price: housePrice,
                term: loanTerm,
                rate: interestRate,
                down: downPayment,


            };

            $.ajax({
                    url: '/getLoanCalculation',
                    type: 'POST',
                    data: postData
                })
                .done(function(response) {
                    console.log(response);
                    // stick the response into the modal html
                    $("#modalCenter .modal-body").html(response);
                    // activate the modal
                    $('#modalCenter').modal('toggle');

                });
        });

    });
</script>



@endsection
