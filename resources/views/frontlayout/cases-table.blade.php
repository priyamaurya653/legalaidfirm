@php
    $allCases = [
        ['LX-2041', 'State vs Rohit S.',        'Rohit Sharma',   'Criminal',  'Arjun Mehta',  '17 Oct 2026', 'Hearing'],
        ['LX-2039', 'Verma vs Builder Ltd.',    'Ankita Verma',   'Civil',     'Priya Sharma', '17 Oct 2026', 'Active'],
        ['LX-2036', 'Meera J. – Custody',       'Meera Joshi',    'Family',    'Priya Sharma', '18 Oct 2026', 'Hearing'],
        ['LX-2033', 'Gupta – GST Appeal',       'Sanjay Gupta',   'Tax',       'Neha Kapoor',  '20 Oct 2026', 'Pending'],
        ['LX-2030', 'Kapoor Industries – NCLT', 'Kapoor Ind.',    'Corporate', 'Neha Kapoor',  '22 Oct 2026', 'Active'],
        ['LX-2027', 'Sharma – Will Registration','Ramesh Sharma', 'Will/GPA',  'Priya Sharma', '—',           'Closed'],
        ['LX-2025', 'Jain – Cheque Bounce',     'Vikas Jain',     'Criminal',  'Arjun Mehta',  '24 Oct 2026', 'Active'],
        ['LX-2021', 'Rao vs Rao – Divorce',     'Sunita Rao',     'Family',    'Priya Sharma', '28 Oct 2026', 'Pending'],
        ['LX-2018', 'Singh – Challan Disposal', 'Pooja Singh',    'Challan',   'Arjun Mehta',  '—',           'Closed'],
        ['LX-2015', 'Mehra Property Partition', 'Anil Mehra',     'Civil',     'Neha Kapoor',  '02 Nov 2026', 'Active'],
    ];
    $rows = array_slice($allCases, 0, $limit ?? count($allCases));
    $statusClass = ['Active' => 'green', 'Hearing' => 'gold', 'Pending' => 'blue', 'Closed' => 'grey'];
@endphp

<div class="table-wrap">
    <table class="adm-table" data-table>
        <thead>
            <tr>
                <th>Case ID</th>
                <th>Matter</th>
                <th class="hide-md">Practice</th>
                <th class="hide-sm">Advocate</th>
                <th class="hide-sm">Next Date</th>
                <th>Status</th>
                <th class="t-right">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $c)
                <tr data-status="{{ $c[6] }}">
                    <td><span class="case-id">{{ $c[0] }}</span></td>
                    <td>
                        <div class="t-title">{{ $c[1] }}</div>
                        <small class="t-sub">{{ $c[2] }}</small>
                    </td>
                    <td class="hide-md"><span class="tag">{{ $c[3] }}</span></td>
                    <td class="hide-sm">{{ $c[4] }}</td>
                    <td class="hide-sm">{{ $c[5] }}</td>
                    <td><span class="status {{ $statusClass[$c[6]] }}">{{ $c[6] }}</span></td>
                    <td class="t-right">
                        <div class="popper-wrap">
                            <button class="icon-btn" data-popper="rowPop{{ ($limit ?? 'all') }}{{ $i }}" aria-label="Actions"><i class="fa-solid fa-ellipsis"></i></button>
                            <div class="popper popper-right" id="rowPop{{ ($limit ?? 'all') }}{{ $i }}">
                                <ul class="pop-menu">
                                    <li><a href="#"><i class="fa-regular fa-eye"></i> View</a></li>
                                    <li><a href="#" data-modal="caseModal"><i class="fa-regular fa-pen-to-square"></i> Edit</a></li>
                                    <li><a href="#"><i class="fa-regular fa-calendar"></i> Add Hearing</a></li>
                                    <li class="sep"></li>
                                    <li><a href="#" class="danger" data-delete-row><i class="fa-regular fa-trash-can"></i> Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>