# Syllabus PDF Export Feature

## Overview

The PDF export feature allows users to generate professional PDF documents of syllabi with proper formatting, including all course information, learning matrices, approval signatures, and institutional branding.

## Features

- **Professional Layout**: Clean, academic-style PDF formatting
- **Complete Content**: Includes all syllabus sections (outcomes, learning matrix, policies, etc.)
- **Approval Status**: Shows current approval status and signatures
- **Version Tracking**: Displays syllabus version and approval dates
- **Institutional Branding**: Includes college logos and department information
- **Security**: Only authenticated users can access PDF exports

## Usage

### From Filament Interface

1. **View PDF**: Click the "View PDF" button to open PDF in browser
2. **Download PDF**: Click "Export PDF" button to download the PDF file

### Direct URLs

- View: `/syllabus/{id}/pdf/view`
- Download: `/syllabus/{id}/pdf/download`

## PDF Sections

1. **Header**: College logo, name, department information
2. **Approval Status**: Current status, version, approval dates
3. **Course Information**: Course details, units, prerequisites
4. **Course Outcomes**: Learning outcomes with action verbs
5. **Learning Matrix**: Weekly breakdown of activities and assessments
6. **References**: Textbooks, digital solutions, online resources
7. **Policies**: Grading system, classroom policies, consultation hours
8. **Signatures**: Preparers and approvers with dates

## Technical Details

### Service Class: `SyllabusPdfService`

- **`generatePdf(Syllabus $syllabus)`**: Creates PDF object
- **`downloadPdf(Syllabus $syllabus, string $filename = null)`**: Downloads PDF
- **`streamPdf(Syllabus $syllabus)`**: Streams PDF to browser

### Template: `resources/views/pdf/syllabus.blade.php`

- Responsive HTML/CSS template
- Print-optimized styling
- Professional academic formatting

### Configuration

- Uses `barryvdh/laravel-dompdf` package
- Configuration in `config/dompdf.php`
- Paper size: A4 Portrait
- DPI: 150 for crisp text and images

## Customization

### Styling

Edit `resources/views/pdf/syllabus.blade.php` to modify:
- Colors and fonts
- Layout and spacing  
- Section organization
- Branding elements

### Data Processing

Modify `SyllabusPdfService` to adjust:
- Data preparation logic
- PDF generation options
- File naming conventions
- Error handling

## Troubleshooting

### Common Issues

1. **Missing Images**: Ensure logo paths are correct in public directory
2. **Font Issues**: DomPDF includes DejaVu Sans by default
3. **Memory Errors**: Increase PHP memory limit for large syllabi
4. **CSS Issues**: Use print-compatible CSS (avoid flexbox/grid)

### Performance

- PDFs are generated on-demand (not cached)
- Large learning matrices may take longer to process
- Consider implementing caching for frequently accessed PDFs

## Security

- Authentication required for all PDF routes
- Permission checks based on user roles
- Syllabus access follows same rules as web interface
