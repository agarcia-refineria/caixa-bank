<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-start gap-4">
                <x-buttons.primary-button
                    class="py-2"
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-create-category')"
                    id="profile-categories-create" shepherd-text="{{ trans('shepherd.profile-categories-create') }}"
                >{{ __('Create Category') }}</x-buttons.primary-button>
                <form id="update-transactions-form" action="{{ route('profile.categories.update-transactions') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <x-buttons.secondary-button class="py-2" onclick="document.getElementById('update-transactions-form').submit();" id="profile-categories-update-transactions" shepherd-text="{{ trans('shepherd.profile-categories-update-transactions') }}">
                    {{ __('Update Transactions') }}
                </x-buttons.secondary-button>
            </div>

            <x-ui.modal name="confirm-create-category" focusable>
                @include('partials.profile.categories.form', ['user' => $user])
            </x-ui.modal>

            @if (count($categories) > 0)
                <div class="grid grid-cols-12 gap-4" id="profile-categories-forms" shepherd-text="{{ trans('shepherd.profile-categories-forms') }}">
                    @foreach ($categories as $category)
                        <div x-data="{ show: false }" class="col-span-12 lg:col-span-6 flex flex-col gap-4">
                            <div class="bg-main2 border-main3 drop-shadow-primary border-2 shadow rounded-lg">
                                @include('partials.profile.categories.form', [
                                    'category' => $category,
                                    'user' => $user,
                                ])

                                <div class="flex justify-center gap-4 pb-4">
                                    @if (count($category->filters) > 0)
                                        <x-buttons.primary-button x-on:click="show = !show">
                                            {{ __('Show Filters') }}
                                        </x-buttons.primary-button>
                                    @endif
                                    <x-buttons.primary-button x-on:click.prevent="$dispatch('open-modal', 'create-filter-{{ $category->id }}')">
                                        {{ __('Create Filter') }}
                                    </x-buttons.primary-button>
                                </div>
                            </div>

                            <div class="shadow rounded-lg">
                                <div class="flex flex-col w-full justify-center gap-4">
                                    <div class="w-full" x-show="show">
                                        @if (count($category->filters) > 0)
                                            <div class="grid grid-cols-1 gap-4 w-full">
                                                @foreach($category->filters as $filter)
                                                    <div class="bg-main2 border-primary border-2 p-4 shadow rounded-lg">
                                                        @include('partials.profile.categories.filter-form', [
                                                            'category' => $category,
                                                            'filter' => $filter
                                                        ])
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if (isset($category))
                                <x-ui.modal name="confirm-category-{{ $category->id }}-deletion" focusable>
                                    <form method="post" action="{{ route('profile.category.destroy', ['id' => $category->id]) }}" class="p-6">
                                        @csrf
                                        @method('delete')

                                        <h2 class="text-lg font-medium text-primary">
                                            {{ __('Are you sure you want to delete your category?') }}
                                        </h2>

                                        <div class="mt-6 flex justify-end">
                                            <x-buttons.secondary-button x-on:click="$dispatch('close')">
                                                {{ __('Cancel') }}
                                            </x-buttons.secondary-button>

                                            <x-buttons.danger-button class="ms-3">
                                                {{ __('Delete Category') }}
                                            </x-buttons.danger-button>
                                        </div>
                                    </form>
                                </x-ui.modal>

                                <x-ui.modal name="create-filter-{{ $category->id }}" focusable>
                                    <div class="py-6">
                                        @include('partials.profile.categories.filter-form', ['category' => $category, 'filter' => null])
                                    </div>
                                </x-ui.modal>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-main2 shadow rounded-lg">
                    <x-ui.empty
                        :title="__('No categories found')"
                        :description="__('Please add a category from create category button.')" />
                </div>
            @endif
        </div>
    </div>

    <script>
        function ajaxUpdateTransactions()
        {
            const url = "{{ route('profile.categories.update-transactions') }}";

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __('An error occurred while updating transactions.') }}');
            });
        }
    </script>
</x-app-layout>
