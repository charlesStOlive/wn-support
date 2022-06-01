<?php namespace Waka\Support\Classes\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use \PhpOffice\PhpSpreadsheet\Shared\Date as DateConvert;
use Waka\Support\Models\TicketGroup;

class TicketGroupsImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    //startKeep/
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if(!$row->filter()->isNotEmpty()) {
                continue;
            }
            $ticketGroup = null;
            $id = $row['id'] ?? null;
            if($id) {
                $ticketGroup = TicketGroup::find($id);
            }
            if(!$ticketGroup) {
                $ticketGroup = new TicketGroup();
            }
            $ticketGroup->id = $row['id'] ?? null;
            $ticketGroup->name = $row['name'] ?? null;
            $ticketGroup->is_factured = $row['is_factured'] ?? null;
            $ticketGroup->save();
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
//            $ticketGroup = null;
//            $id = $row['id'] ?? null;
//            if($id) {
//                $ticketGroup = TicketGroup::find($id);
//             }
//             if(!$ticketGroup) {
//                 $ticketGroup = new TicketGroup();
//             }
//             $ticketGroup->id = $row['id'] ?? null;
//             $ticketGroup->name = $row['name'] ?? null;
//             $ticketGroup->is_factured = $row['is_factured'] ?? null;
//             $ticketGroup->qty = $row['qty'] ?? null;
//             $ticketGroup->heures = $row['heures'] ?? null;
//             $ticketGroup->total = $row['total'] ?? null;
//             $ticketGroup->save();
//         }
//     }
}
