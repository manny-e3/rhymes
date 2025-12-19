# Service Layer Architecture

This document outlines the Service Layer implementation for the Rhymes Platform.

## Architecture Overview

The application follows a clean architecture pattern with clear separation of concerns:

```
Controllers → Services → Models
```

### Key Benefits

1. **Separation of Concerns**: Controllers handle HTTP requests, Services contain business logic, Models handle data persistence
2. **Testability**: Each layer can be unit tested independently
3. **Maintainability**: Business logic is centralized in services
4. **Simplicity**: Reduced complexity by removing unnecessary abstraction layers
5. **Code Reusability**: Services can be used across multiple controllers

## Service Layer

### Core Services

#### WalletService
- Handles wallet analytics and transaction management
- Calculates available balance considering pending payouts
- Manages transaction exports
- Provides wallet overview data

#### PayoutService  
- Manages payout requests and validation
- Calculates payout fees (2.5%)
- Handles payment method updates
- Validates available balance for payouts

#### BookService
- Manages book CRUD operations
- Handles book validation
- Provides sales analytics
- Manages book status updates

### Admin Services

#### BookReviewService
- Handles book review workflow
- Manages author promotions
- Sends status change notifications
- Provides book statistics

#### PayoutManagementService
- Processes payout approvals/denials
- Creates wallet transactions for approved payouts
- Sends payout status notifications
- Provides payout statistics

### Integration Services

#### RevService
- Handles external REV system integration
- Uses RevSyncLog model for logging
- Manages API communication
- Provides connection testing

## Controller Refactoring

### Before (Fat Controllers)
```php
public function index(Request $request)
{
    $user = auth()->user();
    $balance = $user->getWalletBalance();
    
    // Complex query building...
    $transactionsQuery = $user->walletTransactions()->with('book');
    // ... filtering logic
    // ... analytics calculations
    
    return view('author.wallet.index', compact('balance', 'transactions', 'analytics'));
}
```

### After (Thin Controllers)
```php
public function index(Request $request)
{
    $user = auth()->user();
    $filters = $request->only(['type', 'date_from', 'date_to']);
    $walletData = $this->walletService->getWalletOverview($user, $filters);

    return view('author.wallet.index', $walletData);
}
```

## Dependency Injection

### Constructor Injection

Controllers and services use constructor injection:

```php
public function __construct(
    private WalletService $walletService,
    private PayoutService $payoutService
) {
    $this->middleware(['auth', 'role:author|admin']);
}
```

## Error Handling

Services throw appropriate exceptions that controllers catch:

```php
try {
    $this->payoutService->createPayoutRequest($user, $validated);
    return back()->with('success', 'Payout request submitted successfully!');
} catch (\Illuminate\Validation\ValidationException $e) {
    return back()->withErrors($e->errors());
}
```

## Testing Strategy

### Unit Testing

Each layer can be tested independently:

- **Model Tests**: Test data persistence and relationships
- **Service Tests**: Test business logic  
- **Controller Tests**: Mock services, test HTTP handling

### Example Service Test

```php
public function test_can_create_payout_request()
{
    $user = User::factory()->create();
    $walletService = Mockery::mock(WalletService::class);
    
    $walletService->shouldReceive('getAvailableBalance')->andReturn(100.00);
    
    $service = new PayoutService($walletService);
    $result = $service->createPayoutRequest($user, ['amount_requested' => 50.00]);
    
    $this->assertInstanceOf(Payout::class, $result);
}
```

## Migration Guide

### For New Features

1. Create a new service class in `app/Services/`
2. Add business logic to the service
3. Inject the service into your controller
4. Move data access logic to Eloquent models
5. Write unit tests for your service

### For Existing Features

The refactoring has been completed to remove the repository pattern. All services now directly use Eloquent models for data access.