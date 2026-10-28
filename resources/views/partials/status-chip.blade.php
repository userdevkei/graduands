@php $labels = ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'updated' => 'Updated']; @endphp
<span class="chip chip-{{ $status }}">{{ $labels[$status] ?? ucfirst($status) }}</span>
