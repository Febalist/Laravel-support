<div class="alert alert-{{ $type ?? 'primary' }}
{{ ($dismissible ?? false) ? 'alert-dismissible fade show' : '' }}
{{ $class ?? '' }}" role="alert">
  {{ $slot }}
  @if($dismissible ?? false)
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  @endif
</div>
