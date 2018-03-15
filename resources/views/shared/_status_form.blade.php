<form action="{{ route('statuses.store') }}" method="post">
    {{ csrf_field() }}
    @include('shared._errors')

    <textarea class="form-control" rows="3" placeholder="嘤嘤嘤..." name="content">
        {{ old('content') }}
    </textarea>

    <button type="submit" class="btn btn-primary pull-right">发布</button>
</form>