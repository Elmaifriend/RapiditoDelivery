<?php
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Restaurant;
?>

<div class="min-h-screen bg-gray-50 pb-28">

    
    <div class="relative h-52 w-full overflow-hidden bg-gray-200">

        <img
            src="<?php echo e($restaurant->banner_path ? Storage::disk('r2')->temporaryUrl($restaurant->banner_path, now()->addMinutes(10)) : 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?q=80&w=1000&auto=format&fit=crop'); ?>"
            class="h-full w-full object-cover">

        <div class="absolute left-0 right-0 top-0 flex items-center justify-between p-4">

            <button
                onclick="history.back()"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow">
                ←
            </button>

            <button
                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow">
                ♡
            </button>

        </div>
    </div>


    
    <div class="bg-white px-5 pb-5 pt-4">

        <h1 class="text-2xl font-bold text-gray-800">
            <?php echo e($restaurant->name); ?>

        </h1>

        <p class="mt-1 text-sm text-gray-500">
            <?php echo e($restaurant->category?->name); ?> • 25-35 min
        </p>

    </div>


    
    <div class="sticky top-0 z-10 bg-gray-50">

        <div class="no-scrollbar flex gap-3 overflow-x-auto px-4 py-4">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $restaurant->productCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>

                <a
                    href="#category-<?php echo e($category->id); ?>"
                    class="whitespace-nowrap rounded-full border border-gray-200 bg-white px-5 py-2 text-xs font-bold text-gray-600 active:scale-90 transition-transform">

                    <?php echo e($category->name); ?>


                </a>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        </div>

    </div>


    
    <div class="space-y-8 px-4 pt-4">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $restaurant->productCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>

        <div id="category-<?php echo e($category->id); ?>">

            <h2 class="mb-3 text-lg font-bold text-gray-800">
                <?php echo e($category->name); ?>

            </h2>

            <div class="space-y-4">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $category->products->where('is_active', true)->where('is_available', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>

                <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-white p-3">

                    
                    <div class="flex-1 pr-4">

                        <h3 class="text-sm font-bold text-gray-800">
                            <?php echo e($product->name); ?>

                        </h3>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->description): ?>
                        <p class="mt-1 text-xs text-gray-400 line-clamp-2">
                            <?php echo e($product->description); ?>

                        </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <span class="mt-2 block font-bold text-gray-900">
                            $<?php echo e(number_format($product->price, 2)); ?>

                        </span>

                    </div>


                    
                    <div class="flex flex-col items-center gap-2">

                        <div class="relative h-20 w-20 overflow-hidden rounded-xl bg-gray-200">

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->image_path): ?>
                                <img
                                    src="<?php echo e(Storage::url($product->image_path)); ?>"
                                    class="h-full w-full object-cover">
                            <?php else: ?>
                                <img
                                    src="https://placehold.co/150x150"
                                    class="h-full w-full object-cover">
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </div>

                        
                        <button
                            class="rounded-lg border border-red-200 px-4 py-1 text-xs font-bold text-red-500 active:scale-90 transition-transform">

                            Agregar

                        </button>

                    </div>

                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            </div>

        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

    </div>


    
    <div class="fixed bottom-0 left-0 right-0 border-t bg-white p-4">

        <button
            class="w-full rounded-xl bg-red-500 py-3 font-bold text-white shadow-lg">

            Ver carrito

        </button>

    </div>

</div><?php /**PATH C:\Users\Elmaifriend\Documents\Programacion\RapiditoDelivery\storage\framework/views/livewire/views/bdefe7ce.blade.php ENDPATH**/ ?>