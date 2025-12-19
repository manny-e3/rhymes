# Charting Components Documentation

This document explains how to use the new charting components created for the Rhyme App sales data visualization.

## Overview

We've created several reusable Blade components for displaying sales data charts:

1. `sales-chart` - A generic chart component
2. `sales-overview` - A comprehensive sales dashboard component
3. `sales-metrics` - A metrics display component with mini charts
4. `chart-container` - A flexible chart container component

## Components

### 1. Sales Chart Component

A generic chart component that can display various chart types.

**Usage:**
```blade
<x-sales-chart 
    id="myChart"
    title="Revenue Chart"
    type="line"
    :labels="['Jan', 'Feb', 'Mar']"
    :data="[[ 'label' => 'Revenue', 'data' => [1000, 1500, 2000] ]]"
/>
```

**Parameters:**
- `id` - Unique ID for the chart canvas
- `title` - Chart title
- `type` - Chart type (line, bar, doughnut, etc.)
- `labels` - Array of labels for the X-axis
- `data` - Array of dataset objects
- `showLegend` - Whether to show the legend (default: true)

### 2. Sales Overview Component

A comprehensive component that displays multiple sales charts in a dashboard layout.

**Usage:**
```blade
<x-sales-overview 
    :revenue-data="[
        'labels' => ['Jan', 'Feb', 'Mar'],
        'values' => [1000, 1500, 2000]
    ]"
    :metrics-data="[
        'labels' => ['Orders', 'Customers', 'Conversion'],
        'values' => [125, 89, 4.2]
    ]"
    :performance-data="[
        'labels' => ['Jan', 'Feb', 'Mar'],
        'units' => [100, 150, 200],
        'prices' => [25.50, 27.20, 26.80]
    ]"
/>
```

### 3. Sales Metrics Component

Displays key sales metrics with optional mini charts.

**Usage:**
```blade
<x-sales-metrics 
    title1="Total Revenue" 
    value1="$24,560.00" 
    change1="3.2"
    chartId1="revenueMiniChart"
    
    title2="Total Sales" 
    value2="1,248" 
    change2="2.5"
    chartId2="salesMiniChart"
    
    title3="Avg. Order Value" 
    value3="$42.50" 
    change3="-1.2"
    chartId3="aovMiniChart"
    
    title4="Conversion Rate" 
    value4="4.8%" 
    change4="0.8"
    chartId4="conversionMiniChart"
    mini-charts="true"
/>
```

### 4. Chart Container Component

A flexible chart container that can be customized for any chart type.

**Usage:**
```blade
<x-chart-container
    id="revenueChart"
    title="Revenue Trend"
    subtitle="Daily revenue over the last 30 days"
    height="350px"
    type="line"
    :labels="['Jan 1', 'Jan 5', 'Jan 10', 'Jan 15', 'Jan 20', 'Jan 25', 'Jan 30']"
    :datasets="[
        [
            'label' => 'Revenue',
            'data' => [12000, 19000, 15000, 18000, 22000, 25000, 24560],
            'borderColor' => '#559bfb',
            'backgroundColor' => 'rgba(85, 155, 251, 0.1)',
            'borderWidth' => 2,
            'fill' => true,
            'tension' => 0.4
        ]
    ]"
    :format-currency="true"
/>
```

## Routes

A new route has been added for the sales dashboard:

- `GET /admin/reports/sales-dashboard` - Displays the sales dashboard

## Controller Methods

The `ReportsController` now includes a new method:

- `salesDashboard()` - Returns the sales dashboard view

## Views

New views have been created:

1. `resources/views/components/sales-chart.blade.php` - Generic sales chart component
2. `resources/views/components/sales-overview.blade.php` - Sales overview dashboard component
3. `resources/views/components/sales-metrics.blade.php` - Sales metrics component
4. `resources/views/components/chart-container.blade.php` - Flexible chart container component
5. `resources/views/admin/reports/sales-dashboard.blade.php` - Sales dashboard page

## Examples

### Using the Sales Dashboard

To access the new sales dashboard, navigate to:
`/admin/reports/sales-dashboard`

### Creating Custom Charts

You can create custom charts using the chart-container component:

```blade
<x-chart-container
    id="customChart"
    title="Custom Chart"
    type="bar"
    :labels="['A', 'B', 'C', 'D']"
    :datasets="[
        [
            'label' => 'Dataset 1',
            'data' => [10, 20, 30, 40],
            'backgroundColor' => 'rgba(85, 155, 251, 0.7)'
        ]
    ]"
/>
```

## Customization

All components can be customized by modifying their respective Blade files in the `resources/views/components` directory.

## Chart.js

The components use Chart.js for data visualization. Chart.js is loaded via CDN in the views that use the components.

## Best Practices

1. Always provide unique IDs for chart elements
2. Use appropriate chart types for your data (line for trends, bar for comparisons, doughnut for proportions)
3. Format currency values when displaying financial data
4. Include meaningful titles and subtitles for charts
5. Use consistent color schemes across related charts