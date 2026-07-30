<div>
        <flux:heading size="lg">{{ $user->name }}</flux:heading>

        <form wire:submit="save" class="mt-6 max-w-2xl space-y-6">
            <flux:field>
                <flux:label>{{ __('Department') }}</flux:label>
                <flux:select wire:model="departmentId">
                    <option value="">--</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="departmentId" />
            </flux:field>

        <div class="space-y-3">
            <flux:label>{{ __('Roles') }}</flux:label>
            @foreach ($roles as $role)
                <flux:checkbox wire:model="selectedRoles" value="{{ $role->name }}" :label="$role->name" />
            @endforeach
            <flux:error name="selectedRoles" />
        </div>


            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </form>
</div>
