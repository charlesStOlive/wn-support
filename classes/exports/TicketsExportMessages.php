<?php namespace Waka\Support\Classes\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
//
use Waka\Support\Models\Ticket;
use Waka\Support\Models\TicketMessage;

class TicketsExportMessages implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    public $listId;

    public function __construct($listId = null)
    {
        $this->listId = $listId;
    }

    //startKeep/

    public function headings(): array
    {
        return [
            'id',
            'name',
            'ticket_type',
            'temps',
            'state',
            'user',
            'url',
            'ticket_group',
            'awake_at',
            'messages'
        ];
    }

    public function headingsTemps(): array
    {
        return [
            'id',
            'name',
            'ticket_type_id',
            'temps',
            'state',
            'user_id',
            'url',
            'ticket_group_id',
            'awake_at',
        ];
    }

    public function collection()
    {
        $request;
        if ($this->listId) {
            $request = Ticket::whereIn('id', $this->listId)->with('ticket_group', 'ticket_type', 'user')->get($this->headingsTemps());
        } else {
            $request = Ticket::with('ticket_group', 'ticket_type')->get($this->headingsTemps()); 
        }
        $request->transform(function ($item) {
            //trace_log($item->toArray());
            $messages = $item->getMessagesAsTxt();
            $item['messages'] = $messages;
            $item['ticket_group'] = $item['ticket_group']['name'] ?? null; 
            $item['ticket_type'] = $item['ticket_type']['name'] ?? null; 
            $item['user'] = $item['user']['login'] ?? null; 
            return $item;
        });;
        return $request;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A'    => ['font' => ['bold' => true]],
            1 => ['font' => ['bold' => true]],
            'A1:A50' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFF00'],
                ],
            ],
            'J' => [
                'font' => ['size' => 8],
                'alignment' => ['wrapText' => true],
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'J' => 55,            
        ];
    }

    //endKeep/
}
