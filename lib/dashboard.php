<?
function enqueue_chartjs() {
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
}
// Function to add custom dashboard widget
function protection_grid_dashboard_setup() {
	global $protection_grid_svg;
    wp_add_dashboard_widget(
        'protection_grid_uptime', 
        'Protection Grid - Uptime', 
        'protection_grid_dashboard_uptime'
    );
	/*
    wp_add_dashboard_widget(
        'protection_grid_traffic', 
        '<div class="wp-menu-image svg" style="background-image: url(\''.$protection_grid_svg.'\') !important;"></div>Protection Grid - Traffic', 
        'protection_grid_dashboard'
    );
	global $wp_meta_boxes;
	$wporg_widget = $wp_meta_boxes['dashboard']['normal']['core']['protection_grid_traffic'];
	unset( $wp_meta_boxes['dashboard']['normal']['core']['protection_grid_traffic'] );
	$wp_meta_boxes['dashboard']['side']['core']['protection_grid_traffic'] = $wporg_widget;
*/
}
add_action('admin_enqueue_scripts', 'enqueue_chartjs');
add_action('wp_dashboard_setup', 'protection_grid_dashboard_setup' );

// Function to display the content of the custom dashboard widget
function protection_grid_dashboard_uptime() {
    echo '<p>Welcome to my custom dashboard widget!</p>';
	
	$request = protection_grid_API('uptime',protection_grid_data());
	$body = wp_remote_retrieve_body( $request );
    $result = json_decode( $body );
    //print_r($result);
	$data = $result->data->uptime;
    ?>
                                        <div class="row text-center">
											
                                            <div class="col-xs-4">
<?php if($data->day > 99.9){ ?>
                                                	<a href="javascript:void(0)" class="widget-icon themed-background-spring">
                                                    <i class="fa fa-thumbs-up"></i>
<?php }elseif($data->day > 99.8){ ?>
                                                	<a href="javascript:void(0)" class="widget-icon themed-background-autumn">
                                                    <i class="fa fa-exclamation-triangle"></i>
<?php }else{ ?>
                                                	<a href="javascript:void(0)" class="widget-icon themed-background-fire">
                                                    <i class="fa fa-exclamation-triangle"></i>
<?php } ?>
                                                </a>
                                                <h3 class="remove-margin-bottom"><strong><?php print $data->day; ?>%</strong><br><small>Last 24 hours</small></h3>
                                            </div>
                                            <div class="col-xs-4">
<?php if($data->week > 99.9){ ?>
                                                	<a href="javascript:void(0)" class="widget-icon themed-background-spring">
                                                    <i class="fa fa-thumbs-up"></i>
<?php }elseif($data->week > 99.8){ ?>
                                                	<a href="javascript:void(0)" class="widget-icon themed-background-autumn">
                                                    <i class="fa fa-exclamation-triangle"></i>
<?php }else{ ?>
                                                	<a href="javascript:void(0)" class="widget-icon themed-background-fire">
                                                    <i class="fa fa-exclamation-triangle"></i>
<?php } ?>
                                                </a>
                                                <h3 class="remove-margin-bottom"><strong><?php print $data->week; ?>%</strong><br><small>Last 7 days</small></h3>
                                            </div>
                                            <div class="col-xs-4">
<?php if($data->month > 99.9){ ?>
                                                	<a href="javascript:void(0)" class="widget-icon themed-background-spring">
                                                    <i class="fa fa-thumbs-up"></i>
<?php }elseif($data->month > 99.8){ ?>
                                                	<a href="javascript:void(0)" class="widget-icon themed-background-autumn">
                                                    <i class="fa fa-exclamation-triangle"></i>
<?php }else{ ?>
                                                	<a href="javascript:void(0)" class="widget-icon themed-background-fire">
                                                    <i class="fa fa-exclamation-triangle"></i>
<?php } ?>
                                                </a>
                                                <h3 class="remove-margin-bottom"><strong><?php print $data->month; ?>%</strong><br><small>Last Month</small></h3>
                                            </div>
                                        </div>
    <?
}
// Function to display the content of the custom dashboard widget
function protection_grid_dashboard() {
    echo '<p>Welcome to my custom dashboard widget!</p>';
    echo '<canvas id="myChart" width="400" height="400"></canvas>';
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar', // Change to your desired chart type
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>
    <?
}