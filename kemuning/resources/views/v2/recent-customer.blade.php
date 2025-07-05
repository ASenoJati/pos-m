@foreach ($recent_customer as $item)
<tr>
    <td>
        <a
            href="/transaction/{{ $item->track }}"
            class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
            >{{ $item->id }}</a
        >
    </td>
    <td>{{ $item->name }}</td>
    <td>
        @if ($item->hold_order==1) @else @if ($item->progress==1)
        <div
            class="progress mt-2"
            role="progressbar"
            aria-label="Info striped example"
            aria-valuenow="100"
            aria-valuemin="0"
            aria-valuemax="100"
        >
            <div
                class="progress-bar progress-bar-striped bg-success"
                style="width: 100%; border-radius: 0.375rem"
            ></div>
        </div>
        @endif @if ($item->progress==2)
        <div
            class="progress mt-2"
            role="progressbar"
            aria-label="Danger striped example"
            aria-valuenow="60"
            aria-valuemin="30"
            aria-valuemax="100"
        >
            <div
                class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                style="width: 60%; border-radius: 0.375rem"
            ></div>
        </div>
        @endif @if ($item->progress==3)
        <div
            class="progress mt-2"
            role="progressbar"
            aria-label="Danger striped example"
            aria-valuenow="90"
            aria-valuemin="30"
            aria-valuemax="100"
        >
            <div
                class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                style="width: 90%; border-radius: 0.375rem"
            ></div>
        </div>
        @endif @if ($item->progress==null)
        <div
            class="progress mt-2"
            role="progressbar"
            aria-label="Danger striped example"
            aria-valuenow="30"
            aria-valuemin="30"
            aria-valuemax="100"
        >
            <div
                class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                style="width: 30%; border-radius: 0.375rem"
            ></div>
        </div>
        @endif @endif
    </td>
    <td>
        @if ($item->hold_order==1)
        <span class="badge text-bg-warning fs-5"> HOLD ORDER </span>
        @else @if ($item->progress==1)
        <span class="badge text-bg-success fs-5"> FINISH </span>
        @endif @if ($item->progress==2)
        <span class="badge text-bg-primary fs-5"> ON PROCESS </span>
        @endif @if ($item->progress==3)
        <span class="badge text-bg-info fs-5"> WAITING BY CUSTOMER </span>
        @endif @if ($item->progress==null)
        <span class="badge text-bg-danger fs-5"> WAITING FOR QUEUE </span>
        @endif @endif
    </td>
</tr>
@endforeach
