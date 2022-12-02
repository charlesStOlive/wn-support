<?php namespace Waka\Support\Classes\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
//
use Waka\Support\Models\TicketGroup;

class TicketGroupsExportTickets implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithColumnWidths
{
    public $parentId;

    public function __construct($listId, $parentId)
    {
        $this->parentId = $parentId;
    }

    //startKeep/

    public function headings(): array
    {
        return [
            'id',
            'name',
            'code',
            'ticket_type',
            'temps',
            'state',
            'user',
            'next',
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
            'code',
            'ticket_type_id',
            'temps',
            'state',
            'user_id',
            'next_id',
            'url',
            'ticket_group_id',
            'awake_at',
        ];
    }

    public function collection()
    {
        $parent = TicketGroup::find($this->parentId);
        $request = $parent->tickets()->with('ticket_group', 'ticket_type', 'user', 'next')->get($this->headingsTemps());
        $request->transform(function ($item) {
            //trace_log($item->toArray());
            $messages = $item->getMessagesAsTxt();
            $state = 
            $item['messages'] = $messages;
            $item['ticket_group'] = $item['ticket_group']['name'] ?? null; 
            $item['ticket_type'] = $item['ticket_type']['name'] ?? null; 
            $item['user'] = $item['user']['login'] ?? null; 
            $item['next'] = $item['next']['login'] ?? null;
            if($item['state'] != 'sleep') {
                $item['awake_at'] = null;
            }
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
            'L' => [
                'font' => ['size' => 8],
                'alignment' => ['wrapText' => true],
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'L' => 75,            
        ];
    }

    //endKeep/
}