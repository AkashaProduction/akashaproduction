export function Footer({ copy }: { copy: string }) {
  return (
    <footer className="footer">
      <div className="container">
        <div className="glass">
          <strong>Akasha Production</strong>
          <p className="muted">{copy}</p>
        </div>
      </div>
    </footer>
  );
}
