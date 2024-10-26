<!-- home.blade.php -->

@extends('layout')

@section('content')
    <style>
        .table-stats {
            font-size: 0.7rem;
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <form method="post" action={{ route('checkout') }}>
                    @csrf
                    {{-- Store --}}
                    <div class="row text-center align-items-center">
                        <div class="col">
                            <h3>Store</h3>
                            <table class="table table-responsive table-condensed align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($store as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>

                                            <td>
                                                <input type="hidden" name="product[id]" value="{{ $product->id }}" />
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="product[price]"
                                                        class="form-control text-end"
                                                        value="{{ ceil($product->price / 100) }}" />
                                                </div>
                                            </td>

                                            <td>
                                                <input type="number" name="product[quantity]"
                                                    class="form-control text-end @error('client.personal_no') is-invalid @enderror"
                                                    value="{{ $product->quantity }}" />
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>


                    {{-- Users --}}
                    <div class="row align-items-center">
                        @foreach ($users as $user)
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">{{ $user->name }}</h3>

                                        @if (isset($messages[$user->id]))
                                            <div style="height:200px"
                                                class="alert alert-{{ $messages[$user->id]['messageClass'] }}"
                                                role="alert">
                                                {{ $messages[$user->id]['message'] }}
                                            </div>
                                        @endif

                                        @if (!empty($stats[$user->id]))
                                        <table class="table table-condensed table-striped align-middle table-stats">
                                            <tbody>
                                                @foreach ($stats[$user->id] as $statKey => $statData)
                                                    <tr>
                                                        <td width="50%">{{ $statKey }}</td>
                                                        <td width="50%" class="text-end">{{ $statData }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif

                                        <table class="table table-condensed table-striped align-middle">

                                            <tbody>
                                                <tr>
                                                    <td width="50%">Wallet</td>
                                                    <td width="50%">
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input name="users[{{ $user->id }}][balance]"
                                                                type="number" class="form-control text-end"
                                                                value="{{ ceil($user->wallet->balance / 100) }}" />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <input type="hidden"
                                                        name="users[{{ $user->id }}][basket][0][product_id]"
                                                        value="{{ $user->basket->first()->product_id }}" />
                                                    <td width="50%">Basket Qty.</td>
                                                    <td width="50%">
                                                        <input type="number"
                                                            name="users[{{ $user->id }}][basket][0][quantity]"
                                                            class="form-control text-end @error('client.personal_no') is-invalid @enderror"
                                                            value="{{ $user->basket->first()->quantity }}" />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row text-end align-items-center mt-3">
                        <div class="col">
                            <div class="btn-group">
                                <a href="{{ route('index') }}" title="CLEAR STATS" class="btn btn-lg btn-outline-secondary">X</a>
                                <button type="submit" class="btn btn-lg btn-outline-success">CHECKOUT</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
