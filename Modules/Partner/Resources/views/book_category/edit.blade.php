<?php
$formUrl = empty($book_category) ?
    action([\Modules\Partner\Http\Controllers\BookCategoryController::class, 'store']) :
    action([\Modules\Partner\Http\Controllers\BookCategoryController::class, 'update'], [$book_category?->id]);
$title = empty($book_category) ? __('partner::lang.add_book_category') : __('partner::lang.edit_book_category');
?>

<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ $title }}</h4>
        </div>

        <div class="modal-body">
            {!! Form::open(['url' => $formUrl, 'method' => empty($book_category) ? 'post' : 'put', 'id' => 'editBookCategoryForm', 'files' => true]) !!}
            <div class="row">
                <div class="col-sm-12">
                    {!! Form::label('name', __('partner::lang.name') . ':') !!}
                    {!! Form::text('name', $book_category?->name, ['class' => 'form-control', 'placeholder' => __('partner::lang.status'), 'required']) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-save-book-category">@lang('messages.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>
    </div>
</div>