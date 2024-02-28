@extends('layouts.app')

@section('title', 'Transfers')

@section('content')
    <div class="col-12">
        <div class="card">
            <div class="card-title p-3 pb-0 mb-0">
                <h4>Total by Categories:</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">

                    <div class="col-3">
                        <div class="btn-group-vertical align-top">
                            @foreach($years as $year)
                                <a href="{{ route('reports.total-by-categories', ['year' => $year, 'month' => request('month', date('n'))]) }}"
                                    @class(['active' => $year == request('year', date('Y')), 'btn' => true, 'btn-secondary' => true])
                                >
                                    {{ $year }}
                                </a>
                            @endforeach
                        </div>

                        <div class="btn-group-vertical">
                            @foreach($months as $number => $name)
                                <a href="{{ route('reports.total-by-categories', ['month' => $number, 'year' => request('year', date('Y'))]) }}"
                                    @class(['active' => $number == request('month', date('n')), 'btn' => true, 'btn-secondary' => true, 'position-relative' => true])
                                >
                                    {{ $name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-5">
                        <a href="{{ route('reports.total-by-categories', ['year' => date('Y'), 'month' => date('n')]) }}"
                            @class(['btn' => true, 'btn-light' => true])
                        >Current</a>
                        <a class="btn btn-light " href="{{ route('reports.total-by-categories', array_merge(request()->all(), ['filter_category_ids' => $categoryIds])) }}">
                           Filter All
                        </a>
                        <table class="table">
                            <thead>
                            <tr>
                                <th><strong>Total</strong></th>
                                <th>{{ $total }}</th>
                            </tr>
                            <tbody>
                            @foreach($totalByCategories as $item)
                                <tr @class(['table-danger' => in_array($item['categoryId'], request('filter_category_ids', []))])>
                                    <td>
                                        @if (in_array($item['categoryId'], $filterCategoryIds))
                                            <a class="btn btn-sm btn-light"
                                               href="{{ route('reports.total-by-categories', array_merge(request()->all(), ['filter_category_ids' => array_diff($filterCategoryIds, [$item['categoryId']])])) }}"
                                            >
                                                +
                                            </a>
                                        @else
                                            <a class="btn btn-sm btn-light"
                                               href="{{ route('reports.total-by-categories', array_merge(request()->all(), ['filter_category_ids' => array_merge($filterCategoryIds, [$item['categoryId']])])) }}"
                                            >
                                                –
                                            </a>
                                        @endif

                                        <a class="text-body" href="{{ route('categories.show', $item['categoryId']) }}">{{ $item['categoryName'] }}</a>
                                    </td>
                                    <td>{{ $item['total'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th><strong>Total</strong></th>
                                <th>{{ $total }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
