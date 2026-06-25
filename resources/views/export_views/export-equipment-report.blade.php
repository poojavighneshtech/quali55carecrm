<table class="table table-stripped">
    <thead>
        <tr>
            <th>Sr. No</th>
            <th>Equipment Name</th>
            <th>Total Count</th>
            <th>Sale</th>
            <th>Rental</th>
            <th>Live</th>
            <th>Stop</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sr_no = 1;    
        @endphp
        @forelse ($equipment_report as $key=>$equipment)
            <tr>
                <td>{{$sr_no}}</td>
                <td>{{$equipment->name}}</td>
                <td>{{$equipment->sale + $equipment->rental}}</td>
                <td>{{$equipment->sale}}</td>
                <td>{{$equipment->rental}}</td>
                <td>{{$equipment->live}}</td>
                <td>{{$equipment->stop}}</td>
            </tr>
            @php
                $sr_no++;
            @endphp
        @empty
            <tr>
                <td colspan="5" class="text-center">No Records Found</td>
            </tr>
        @endforelse
    </tbody>
</table>