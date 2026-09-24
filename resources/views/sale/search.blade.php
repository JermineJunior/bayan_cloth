@foreach ($products as $product)
    <tr>
        <th>{{ $loop->iteration }}</th>
        <th>{{ $product->name }}</th>
            <th>{{ $product->stocks()->where('stock_id',$stock_id)->sum('quantity') }}
        </th>
        <th><a href="#" class="btn btn-primary btn-add-invoice" data-id="{{ $product->id }}"><i
                    class="fa fa-plus"></i></a>
        </th>
    </tr>
@endforeach
