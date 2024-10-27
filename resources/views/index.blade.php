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
                    <div class="row text-center align-items-center mt-3">
                        <div class="col">
                            <table class="table table-responsive table-condensed align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $product->name }}</td>

                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" min="1" name="product[price]"
                                                    class="form-control text-end @error('product.price') is-invalid @enderror"
                                                    value="{{ $errors->first('product.price') ? ceil(old('product.price') / 100) : ceil($product->price / 100) }}" />
                                                @error('product.price')
                                                    <small
                                                        class="invalid-feedback">{{ $errors->first('product.price') }}</small>
                                                @enderror
                                            </div>
                                        </td>

                                        <td>
                                            <input type="number" min="0" name="product[quantity]"
                                                class="form-control text-end @error('product.quantity') is-invalid @enderror"
                                                value="{{ $errors->first('product.quantity') ? old('product.quantity') : $product->quantity }}" />
                                            @error('product.quantity')
                                                <small class="invalid-feedback">{{ $errors->first('product.quantity') }}</small>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    {{-- Users --}}
                    <div class="row align-items-start">
                        @foreach ($users as $user)
                            <div class="col-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">{{ $user->name }}</h3>

                                        @if (isset($messages[$user->id]))
                                            <div style="height:100px"
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
                                                            <input min="0" name="users[{{ $user->id }}][balance]"
                                                                type="number"
                                                                class="form-control text-end @error("users.{$user->id}.balance") is-invalid @enderror"
                                                                value="{{ $errors->first("users.{$user->id}.balance") ? old("users.{$user->id}.balance") : ceil($user->getBalance() / 100) }}" />
                                                            @error("users.{$user->id}.balance")
                                                                <small
                                                                    class="invalid-feedback">{{ $errors->first("users.{$user->id}.balance") }}</small>
                                                            @enderror
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="50%">Basket Qty.</td>
                                                    <td width="50%">
                                                        <input type="number" min="0" name="users[{{ $user->id }}][quantity]"
                                                            class="form-control text-end @error("users.{$user->id}.quantity") is-invalid @enderror"
                                                            value="{{ $errors->first("users.{$user->id}.quantity") ? old("users.{$user->id}.quantity") : $user->basket->first()->quantity }}" />
                                                        @error("users.{$user->id}.quantity")
                                                            <small
                                                                class="invalid-feedback">{{ $errors->first("users.{$user->id}.quantity") }}</small>
                                                        @enderror
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="50%">Orders</td>
                                                    <td width="50%" class="text-end">
                                                        <input type="number" readonly disabled
                                                            class="form-control text-end"
                                                            value="{{ $user->orders->count() }}" />
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
                                <a href="{{ route('index') }}" title="CLEAR STATS"
                                    class="btn btn-lg btn-outline-secondary">X</a>
                                <a href="{{ route('reset') }}" class="btn btn-lg btn-outline-secondary">RESET</a>
                                <button type="submit" class="btn btn-lg btn-outline-success">BUY NOW</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
