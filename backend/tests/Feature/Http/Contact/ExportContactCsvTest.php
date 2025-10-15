<?php

use App\Services\Contact\ContactService;
use App\Repositories\Contact\ContactRepository;
use App\Exports\ContactsExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use RuntimeException;

beforeEach(function () {
    $this->repo = mock(ContactRepository::class);
    $this->service = new ContactService($this->repo);
});

it('should return a CSV file successfully when exporting contacts', function () {
    // Arrange
    Excel::fake();

    $ids = [1, 2, 3];

    // Act
    $response = $this->service->exportCsv($ids);

    // Assert
    expect($response)->toBeInstanceOf(BinaryFileResponse::class);

    Excel::assertDownloaded(
        'contatos_' . now()->format('Ymd_His') . '.csv',
        function ($export) {
            return $export instanceof ContactsExport;
        }
    );
});

it('should log an error and throw a RuntimeException when export fails', function () {
    // Arrange
    Excel::shouldReceive('download')
        ->andThrow(new Exception('Excel export failed'));
    Log::spy();

    // Act & Assert
    expect(fn() => $this->service->exportCsv([99]))
        ->toThrow(RuntimeException::class, 'Erro ao exportar contatos.');

    Log::shouldHaveReceived('error')
        ->once()
        ->withArgs(
            fn($message, $context) =>
            str_contains($message, 'Erro ao exportar contatos')
                && isset($context['ids'])
        );
});
