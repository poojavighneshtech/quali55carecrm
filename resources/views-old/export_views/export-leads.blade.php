                    <table id="" class="table table-hover table-flush">
                        <thead class="thead thead-light text-dark border-primary">
                            <tr class="text-nowrap border-primary">
                                <th>Cr Date</th>
                                <th>Customer Name</th>
                                <th>Patient Name</th>
                                <th>Mobile No</th>
                                <th>Products</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Lead Source</th>
                                <th>Lead Owner</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($get_all_leads as $key => $lead)
                                <tr>
                                    <td>{{date('d-M-y',strtotime($lead->creation_date))}} {{date('h:i A',strtotime($lead->created_at))}}</td>
                                    <td>{{$lead->customer_name}}</td>
                                    <td>{{$lead->patient_name}}</td>
                                    <td>{{$lead->primary_contact_no}}</td>
                                    <td>{{$json_decode_all_leads['data'][$key]['product_name']}}</td>
                                    <td>{{$lead->location}}</td>
                                    <td>{{$lead->lead_status}}</td>
                                    <td>{{$lead->lead_source}}</td>
                                    <td>{{$lead->lead_owner}}</td>
                                    <td>{{$lead->lead_comment}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                