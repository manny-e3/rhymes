<div class="<?php echo e($class ?? 'card'); ?>">
    <div class="<?php echo e(isset($class) ? '' : 'card-inner'); ?>">
        <div class="card-title-group <?php echo e($titleClass ?? 'mb-3'); ?>">
            <?php if(isset($title)): ?>
                <div class="card-title">
                    <h6 class="title"><?php echo e($title); ?></h6>
                    <?php if(isset($subtitle)): ?>
                        <p><?php echo e($subtitle); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($actions) || isset($dropdown)): ?>
                <div class="card-tools">
                    <?php if(isset($actions)): ?>
                        <?php echo $actions; ?>

                    <?php endif; ?>
                    
                    <?php if(isset($dropdown)): ?>
                        <div class="dropdown">
                            <a href="#" class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown"><?php echo e($dropdown['label'] ?? 'Options'); ?></a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <ul class="link-list-opt no-bdr">
                                    <?php $__currentLoopData = $dropdown['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><a href="<?php echo e($item['url'] ?? '#'); ?>" onclick="<?php echo e($item['onclick'] ?? ''); ?>"><span><?php echo e($item['label']); ?></span></a></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="chart-container" style="<?php echo e(isset($height) ? 'height: ' . $height . ';' : 'height: 300px;'); ?>">
            <canvas id="<?php echo e($id ?? 'chart'); ?>"></canvas>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('<?php echo e($id ?? 'chart'); ?>').getContext('2d');
    
    // Chart configuration from component attributes
    const config = {
        type: '<?php echo e($type ?? 'line'); ?>',
        data: {
            labels: <?php echo json_encode($labels ?? [], 15, 512) ?>,
            datasets: <?php echo json_encode($datasets ?? [], 15, 512) ?>
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: <?php echo e($showLegend ?? 'true'); ?>

                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                // Format currency if specified
                                if (<?php echo e(isset($formatCurrency) && $formatCurrency ? 'true' : 'false'); ?>) {
                                    label += new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'USD'
                                    }).format(context.parsed.y);
                                } else {
                                    label += context.parsed.y;
                                }
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: <?php echo e($beginAtZero ?? 'true'); ?>,
                    ticks: {
                        callback: function(value) {
                            // Format currency on Y axis if specified
                            if (<?php echo e(isset($formatCurrency) && $formatCurrency ? 'true' : 'false'); ?>) {
                                return '$' + value;
                            }
                            return value;
                        }
                    }
                }
            }
        }
    };
    
    // Extend config with additional options if provided
    <?php if(isset($options)): ?>
        Object.assign(config.options, <?php echo json_encode($options, 15, 512) ?>);
    <?php endif; ?>
    
    new Chart(ctx, config);
});
</script>
<?php $__env->stopPush(); ?><?php /**PATH /home/rovinghe/author.rovingheights.com/resources/views/components/chart-container.blade.php ENDPATH**/ ?>