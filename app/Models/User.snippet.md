Add to your existing `App\Models\User`:

```php
protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

protected function casts(): array   // Laravel 11 (use $casts property on 10)
{
    return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];
}

public function isSuperAdmin(): bool
{
    return $this->role === 'super_admin';
}
```
