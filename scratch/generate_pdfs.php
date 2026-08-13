<?php
function create_pdf($filename, $title, $subject, $bodyText) {
    $streamContent = "BT\n/F1 18 Tf\n50 720 Td\n(" . addslashes($title) . ") Tj\n/F1 12 Tf\n0 -30 Td\n(" . addslashes($subject) . ") Tj\n0 -40 Td\n(" . addslashes($bodyText) . ") Tj\n0 -20 Td\n(ITS-BERT Intelligent Tutoring System Material) Tj\nET";
    $streamLen = strlen($streamContent);

    $pdf = "%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>
endobj
4 0 obj
<< /Length {$streamLen} >>
stream
{$streamContent}
endstream
endobj
5 0 obj
<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>
endobj
xref
0 6
0000000000 65535 f 
0000000010 00000 n 
0000000060 00000 n 
0000000117 00000 n 
0000000245 00000 n 
0000000450 00000 n 
trailer
<< /Size 6 /Root 1 0 R >>
startxref
550
%%EOF";
    file_put_contents(__DIR__ . '/../' . $filename, $pdf);
}

create_pdf('com211_java_notes.pdf', 'COM211 Object Oriented Programming Java Notes', 'ITS-BERT Tutoring System Lecture Notes', 'Comprehensive reference guide for Java OOP Classes Inheritance and Interfaces.');
create_pdf('com212_ds_guide.pdf', 'COM212 Data Structures and Algorithms Handbook', 'ITS-BERT Tutoring System Study Guide', 'In depth analysis of Arrays Linked Lists Trees Graphs and Algorithm Complexity.');
create_pdf('com213_sql_cheatsheet.pdf', 'COM213 Relational Database and SQL Cheatsheet', 'ITS-BERT Tutoring System Reference Sheet', 'Complete SQL DDL DML queries Joins Group By Normalization and Indexing.');
create_pdf('com214_web_slides.pdf', 'COM214 Web Engineering and PHP Security Slides', 'ITS-BERT Tutoring System Presentation Deck', 'Modern Web Tech HTML5 CSS3 JS ES6 PHP backend and MySQL security.');

echo "All 4 PDF files generated successfully!\n";
