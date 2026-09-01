<?php 


interface PaymentGetway 
{
    public function charge(float $amount): bool ;
}

class CheckoutService 
{
    public function __construct(
        private PaymentGetway $getway
    ){}

    public function checkout(float $amount): bool 
    {
        return $this->getway->charge($amount);
    }

}

class StripePaymentGetway implements PaymentGetway 
{
    public function charge(float $amount): bool 
    {
        echo "Stripe Payment Done: {$amount}." . PHP_EOL;
        return true;
    }

    
}

$checkout = new CheckoutService(
    new StripePaymentGetway()
);

$checkout->checkout(1000);



interface ReportFormatter 
{
    public function format(array $data): string;
}

class JsonReportFormatter implements ReportFormatter 
{
    public function format(array $data): string 
    {
        return json_encode($data);
    }
}

class CsvReportFormatter implements ReportFormatter 
{
    public function format(array $data): string 
    {
        $csv = '';
        foreach ($data as $row) {
            $csv .= implode(',', $row) . PHP_EOL;
        }
        return $csv;
    }
}

class PdfReportFormatter implements ReportFormatter 
{
    public function format(array $data): string 
    {
        // For simplicity, we'll just return a string representation of the data
        return "PDF Report: " . print_r($data, true);
    }
}

class ReportGenerator
{
    public function __construct(
        private ReportFormatter $formatter
    ){}

    public function generate(array $data): string 
    {
        return $this->formatter->format($data);
    }
}

$reportGenerateInJson = new ReportGenerator(
    new JsonReportFormatter()
);

$data = [
    ['name' => 'John Doe', 'email' => 'john@example.com'],
    ['name' => 'Jane Smith', 'email' => 'jane@example.com']
];

echo $reportGenerateInJson->generate($data) . PHP_EOL;


$reportGenerateInCsv = new ReportGenerator(
    new CsvReportFormatter()
);

echo $reportGenerateInCsv->generate($data) . PHP_EOL;