{{-- old fontawesome icon picker markup --}}
{{-- <div class="form-group">
    <label for="icon" class="d-block">{{__('Icon')}}</label>
    <div class="btn-group edit_icon">
        <button type="button" class="btn btn-primary iconpicker-component">
            <i class="las la-edit"></i>
        </button>
        <button type="button" class="icp icp-dd btn btn-primary dropdown-toggle"
                data-selected="las la-edit" data-bs-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">{{__('Toggle Dropdown')}}</span>
        </button>
        <div class="dropdown-menu"></div>
    </div>
    <input type="hidden" class="form-control"  id="edit_social_icon" value="las la-user" name="icon">
</div> --}}

{{-- new tabler icon picker markup --}}
<div class="form-group">
    <label for="icon" class="d-block">{{__('Icon')}}</label>
    <div class="btn-group edit_icon">
        <button type="button" class="btn btn-primary iconpicker-component">
            <i class="ti tabler-edit"></i>
        </button>
        <button type="button" class="icp icp-dd btn btn-primary">
            <span class="sr-only">{{__('Pick Icon')}}</span>
            {{__('Choose Icon')}}
        </button>
    </div>
    <input type="hidden" class="form-control" id="edit_social_icon" value="ti tabler-user" name="icon">
</div>
