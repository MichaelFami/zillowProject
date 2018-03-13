@extends('master')
@section('styles')
<link rel="stylesheet" href="css/style.css">
@endsection

@section('content')

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
             <input type="text" name="zipListing" placeholder="Zipcode" value="">
             <input type="text" name="numberComps" placeholder="Number of Comps" value="">
             <button class="button btn" type="submit" name="button">Search</button>
         </form>
     </div>
 </div>
<div class="row">
    <div class="col-8">

        <div class="row sepRows">
            <div class="col">
                <table>
                    <thead>
                        <th>Address</th>
                        <th>City</th>
                        <th>Value</th>
                        <th>Square Feet</th>
                        <th>Price Per SF</th>
                        <th>Listing</th>
                    </thead>
                    <tbody>
                        @if(!empty($compData))
                        <tr>
                            <td>{{$compData['response']['properties']['principal']['address']['street']}}</td>
                            <td>{{$compData['response']['properties']['principal']['address']['city']}}</td>
                            @if( !is_array($compData['response']['properties']['principal']['zestimate']['amount']) )
                                <td>${{$compData['response']['properties']['principal']['zestimate']['amount']}}</td>
                            @else
                                <td>
                                    No Estimate Available
                                </td>
                            @endif
                            @if( !is_array($compData['response']['properties']['principal']['finishedSqFt']) )
                                <td>{{$compData['response']['properties']['principal']['finishedSqFt']}}</td>
                            @else
                                <td>
                                    No Square Feet Available
                                </td>
                            @endif

                            <td>{{($compData['response']['properties']['principal']['zestimate']['amount']/$compData['response']['properties']['principal']['finishedSqFt'])}}</td>
                            <td><a href="{{$compData['response']['properties']['principal']['links']['homedetails']}}" target="_blank">
                                <button class="btn button"type="button"  name="button" >Listing</button></a></td>

                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table>
                    <thead>
                        <th>Address</th>
                        <th>City</th>
                        <th>Value</th>
                        <th>Square Feet</th>
                        <th>Price Per SF</th>
                        <th>Listing</th>
                    </thead>
                    <tbody>
                        @if(!empty($compData))
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
                            @endif
                            @if( !empty($comp['finishedSqFt']) )
                                <td>{{$comp['finishedSqFt']}}</td>
                                <td>{{($comp['zestimate']['amount']/$comp['finishedSqFt'])}}</td>


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
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col">
        <h5>Average Price Per Square Foot</h5>
        <div class="row">

            <div class="col">
                @if(!empty($compData))
                    <?php $sum= 0;  ?>
                @foreach($compData['response']['properties']['comparables']['comp'] as $comp)

                    @if( !empty($comp['finishedSqFt']) )
                    <?php $ppsf = ($comp['zestimate']['amount']/$comp['finishedSqFt']);
                        $sum += $ppsf;

                        ?>

                    @else
                    <?php $sum += 0;

                     ?>

                    @endif
                @endforeach
                <?php
                $avppsf = $sum/count($compData['response']['properties']['comparables']['comp']);

                echo $avppsf; ?>
                @endif
            </div>
        </div>
        <h5>Investment Recommendation</h5>
        <div class="row">
            <div class="col">
                @if(!empty($compData))
                <?php if (($compData['response']['properties']['principal']['zestimate']['amount']/$compData['response']['properties']['principal']['finishedSqFt']) < $avppsf) {
                    echo "Buy";
                } else{
                    echo "Do Not Buy";
                }?>
                @endif
            </div>
        </div>
    </div>
</div>


@endsection
