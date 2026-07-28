@extends('layouts.utama')

@section('title', 'Daftar Pantauan')
@section('page-title', 'Daftar Pantauan Anda')

@section('content')
<div class="row g-4" id="watchlistContainer">
    @forelse($watchlists as $wl)
    <div class="col-md-4" id="watchlist-card-{{ $wl->negara->id }}">
        <div class="neo-box neo-box-hover h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="neo-badge neo-badge-purple fw-bold">{{ $wl->negara->code }}</span>
                    @if($wl->negara->flag_url)
                        <img src="{{ $wl->negara->flag_url }}" alt="Flag" style="width: 32px; border: 1px solid #000; border-radius: 4px;">
                    @endif
                </div>

                <h4 class="fw-bold mb-2 text-dark">{{ $wl->negara->name }}</h4>
                <p class="text-secondary small mb-3">Wilayah: <span class="fw-bold">{{ $wl->negara->region }}</span></p>

                <!-- Latest Risk -->
                @if($wl->negara->latestRisk)
                    <div class="border border-dark border-2 p-3 bg-light text-center mb-3" style="box-shadow: 2px 2px 0px #000; border-radius: 6px;">
                        <span class="text-secondary small fw-bold">Skor Risiko Terbobot</span>
                        <h3 class="fw-bold my-1">{{ $wl->negara->latestRisk->total_score }}</h3>
                        <span class="neo-badge {{ $wl->negara->latestRisk->risk_level === 'Tinggi' ? 'neo-badge-pink' : ($wl->negara->latestRisk->risk_level === 'Sedang' ? 'neo-badge-yellow' : 'neo-badge-lime') }} py-1 px-3">
                            RISIKO {{ strtoupper($wl->negara->latestRisk->risk_level) }}
                        </span>
                    </div>
                @else
                    <div class="border border-dark border-2 p-3 bg-white text-center text-secondary small mb-3" style="box-shadow: 2px 2px 0px #000; border-radius: 6px; border-style: dashed !important;">
                        <p class="mb-0">Skor risiko belum dihitung.</p>
                    </div>
                @endif
                
                @if($wl->notes)
                    <div class="bg-white border border-dark border-2 p-2 mb-3 small" style="box-shadow: 1px 1px 0px #000; border-radius: 4px;">
                        <strong>Catatan Pantauan:</strong><br>
                        <span class="text-secondary">{{ $wl->notes }}</span>
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-top border-dark d-flex gap-2">
                <a href="{{ route('countries.show', $wl->negara->code) }}" class="btn-neo btn-neo-blue w-50 py-2 small fw-bold" style="font-size: 0.75rem;">DETAIL</a>
                <button onclick="hapusPantauan({{ $wl->negara->id }})" class="btn-neo btn-neo-pink text-white w-50 py-2 small fw-bold" style="font-size: 0.75rem;">HAPUS</button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center">
        <div class="neo-box py-5">
            <h4 class="text-secondary mb-0">Belum ada negara dalam daftar pantauan Anda.</h4>
            <p class="text-secondary small mt-2 mb-0">Cari negara di menu "Daftar Negara" dan klik tombol bintang untuk menambahkan pantauan.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection

@section('scripts')
<script>
function hapusPantauan(countryId) {
    if (confirm('Apakah Anda yakin ingin menghapus negara ini dari daftar pantauan Anda?')) {
        showLoader();
        $.ajax({
            url: "{{ route('watchlist.toggle') }}",
            method: 'POST',
            data: {
                country_id: countryId
            },
            success: function(res) {
                hideLoader();
                if (res.success && res.status === 'removed') {
                    // Hapus elemen card dari view
                    $('#watchlist-card-' + countryId).fadeOut(400, function() {
                        $(this).remove();
                        if ($('#watchlistContainer').children().length === 0) {
                            $('#watchlistContainer').html(
                                '<div class="col-12 text-center">' +
                                '<div class="neo-box py-5">' +
                                '<h4 class="text-secondary mb-0">Belum ada negara dalam daftar pantauan Anda.</h4>' +
                                '</div>' +
                                '</div>'
                            );
                        }
                    });
                }
            },
            error: function() {
                hideLoader();
                alert('Gagal menghapus negara dari watchlist.');
            }
        });
    }
}
</script>
@endsection
