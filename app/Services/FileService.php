<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;

class FileService
{
    public function extractText(string $path, ?string $type = null): string
    {
        if (!Storage::disk('local')->exists($path)) {
            Log::error("File not found at path: {$path}");
            return "";
        }

        $fullPath = Storage::disk('local')->path($path);
        $extension = $type ?? pathinfo($fullPath, PATHINFO_EXTENSION);

        return match (strtolower($extension)) {
            'pdf' => $this->extractFromPdf($fullPath),
            'docx', 'doc' => $this->extractFromWord($fullPath),
            'odt' => $this->extractFromOdt($fullPath),
            'mp4', 'mov', 'avi', 'wmv', 'webm' => "", // Handled by AI service directly
            default => "",
        };
    }

    private function extractFromPdf(string $fullPath): string
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($fullPath);
            return $pdf->getText();
        } catch (\Exception $e) {
            Log::error("Error extracting text from PDF: " . $e->getMessage());
            return "";
        }
    }

    private function extractFromWord(string $fullPath): string
    {
        try {
            $phpWord = IOFactory::load($fullPath);
            $text = "";
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        // Handle nested elements like tables
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText() . "\n";
                            }
                        }
                    }
                }
            }
            return $text;
        } catch (\Exception $e) {
            Log::error("Error extracting text from Word: " . $e->getMessage());
            return "";
        }
    }

    private function extractFromOdt(string $fullPath): string
    {
        try {
            $phpWord = IOFactory::load($fullPath, 'ODText');
            $text = "";
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            return $text;
        } catch (\Exception $e) {
            Log::error("Error extracting text from ODT: " . $e->getMessage());
            return "";
        }
    }
}
