<?php namespace Waka\Support\Classes\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Waka\Support\Models\Ticket;

class TicketsImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    //startKeep/
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if(!$row->filter()->isNotEmpty()) {
                continue;
            }
            $ticket = null;
            $id = $row['id'] ?? null;
            if($id) {
                $ticket = Ticket::find($id);
            }
            if(!$ticket) {
                $ticket = new Ticket();
            }
            $ticket->id = $row['id'] ?? null;
            $ticket->name = $row['name'] ?? null;
            $ticket->ticket_type_id = $row['ticket_type_id'] ?? null;
            $ticket->state = $row['state'] ?? null;
            $ticket->user_id = $row['user_id'] ?? null;
            $ticket->subject = $row['subject'] ?? null;
            $ticket->url = $row['url'] ?? null;
            $ticket->save();
        }
    }
    //endKeep/


    /**
     * SAUVEGARDE DES MODIFS MC
     */
//     public function collection(Collection $rows)
//     {
//        foreach ($rows as $row) {
//            if(!$row->filter()->isNotEmpty()) {
//                continue;
//            }
//            $ticket = null;
//            $id = $row['id'] ?? null;
//            if($id) {
//                $ticket = Ticket::find($id);
//             }
//             if(!$ticket) {
//                 $ticket = new Ticket();
//             }
//             $ticket->id = $row['id'] ?? null;
//             $ticket->name = $row['name'] ?? null;
//             $ticket->ticket_type_id = $row['ticket_type_id'] ?? null;
//             $ticket->state = $row['state'] ?? null;
//             $ticket->user_id = $row['user_id'] ?? null;
//             $ticket->url = $row['url'] ?? null;
//             $ticket->save();
//         }
//     }
}
